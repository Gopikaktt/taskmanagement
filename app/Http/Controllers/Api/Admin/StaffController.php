<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Http\Resources\StaffResource;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    /**
     * Display a listing of staff members.
     */
    public function index(Request $request): JsonResponse|Response
    {
        $query = Staff::with(['roles'])->withCount('tasks');

        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
        } elseif ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        $staff = $query->latest()->paginate($request->input('per_page', 10))->withQueryString();

        if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
            return response()->json([
                'success' => true,
                'data' => StaffResource::collection($staff),
                'meta' => [
                    'current_page' => $staff->currentPage(),
                    'last_page' => $staff->lastPage(),
                    'per_page' => $staff->perPage(),
                    'total' => $staff->total(),
                ],
            ]);
        }

        $departments = Staff::distinct()->whereNotNull('department')->pluck('department');

        return Inertia::render('Admin/Staff/Index', [
            'staff' => $staff,
            'departments' => $departments,
            'filters' => $request->only(['search', 'status', 'department']),
        ]);
    }

    /**
     * Authorize staff management permissions for the authenticated admin.
     */
    protected function authorizeStaffManagement(Request $request): void
    {
        $admin = $request->user() ?? Auth::guard('admin')->user();

        if (! $admin || ! $admin->can('manage staff')) {
            if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
                abort(response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: You do not have permission to manage staff members.',
                ], 403));
            }

            abort(403, 'Unauthorized: You do not have permission to manage staff members.');
        }
    }

    /**
     * Show form for creating a new staff member.
     */
    public function create(): Response
    {
        $this->authorizeStaffManagement(request());

        $roles = Role::where('guard_name', 'staff')->pluck('name');

        return Inertia::render('Admin/Staff/Create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created staff member.
     */
    public function store(StoreStaffRequest $request): JsonResponse|RedirectResponse
    {
        $this->authorizeStaffManagement($request);

        $validated = $request->validated();
        $role = $validated['role'] ?? null;
        unset($validated['role']);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['email_verified_at'] = $request->boolean('is_verified', true) ? now() : null;

        $staff = Staff::create($validated);

        if ($role) {
            $staff->syncRoles([$role]);
        }

        if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
            return response()->json([
                'success' => true,
                'message' => 'Staff member created successfully.',
                'data' => new StaffResource($staff->loadCount('tasks')),
            ], 201);
        }

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member created successfully!');
    }

    /**
     * Display the specified staff member.
     */
    public function show(Request $request, Staff $staff): JsonResponse|Response
    {
        $staff->load(['roles', 'tasks.project'])->loadCount('tasks');

        if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
            return response()->json([
                'success' => true,
                'data' => new StaffResource($staff),
            ]);
        }

        $departments = Staff::distinct()->whereNotNull('department')->pluck('department');

        return Inertia::render('Admin/Staff/Index', [
            'staff' => Staff::with('roles')->withCount('tasks')->paginate(10),
            'departments' => $departments,
        ]);
    }

    /**
     * Show form for editing the staff member.
     */
    public function edit(Staff $staff): Response
    {
        $this->authorizeStaffManagement(request());

        $staff->load('roles');
        $roles = Role::where('guard_name', 'staff')->pluck('name');

        return Inertia::render('Admin/Staff/Edit', [
            'staff' => $staff,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified staff member.
     */
    public function update(UpdateStaffRequest $request, Staff $staff): JsonResponse|RedirectResponse
    {
        $this->authorizeStaffManagement($request);

        $validated = $request->validated();
        $role = $validated['role'] ?? null;
        unset($validated['role']);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if (array_key_exists('is_verified', $validated)) {
            $validated['email_verified_at'] = $validated['is_verified'] ? now() : null;
            unset($validated['is_verified']);
        }

        $staff->update($validated);

        if ($role) {
            $staff->syncRoles([$role]);
        }

        if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
            return response()->json([
                'success' => true,
                'message' => 'Staff member updated successfully.',
                'data' => new StaffResource($staff->fresh()->loadCount('tasks')),
            ]);
        }

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member updated successfully!');
    }

    /**
     * Remove the specified staff member.
     */
    public function destroy(Request $request, Staff $staff): JsonResponse|RedirectResponse
    {
        $this->authorizeStaffManagement($request);

        $staff->delete();

        if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
            return response()->json([
                'success' => true,
                'message' => 'Staff member deleted successfully.',
            ]);
        }

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member deleted successfully.');
    }

    /**
     * Toggle active status of staff account.
     */
    public function toggleStatus(Request $request, Staff $staff): JsonResponse|RedirectResponse
    {
        $this->authorizeStaffManagement($request);

        $staff->is_active = ! $staff->is_active;
        $staff->save();

        $statusMsg = "Staff account is now " . ($staff->is_active ? 'Active' : 'Deactivated') . ".";

        if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
            return response()->json([
                'success' => true,
                'message' => $statusMsg,
                'data' => new StaffResource($staff),
            ]);
        }

        return redirect()->back()->with('success', $statusMsg);
    }

    /**
     * Toggle email verification status of staff account.
     */
    public function toggleVerification(Request $request, Staff $staff): JsonResponse|RedirectResponse
    {
        $this->authorizeStaffManagement($request);

        $staff->email_verified_at = $staff->email_verified_at ? null : now();
        $staff->save();

        $verifyMsg = "Staff email verification is now " . ($staff->isVerified() ? 'Verified' : 'Unverified') . ".";

        if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
            return response()->json([
                'success' => true,
                'message' => $verifyMsg,
                'data' => new StaffResource($staff),
            ]);
        }

        return redirect()->back()->with('success', $verifyMsg);
    }
}
