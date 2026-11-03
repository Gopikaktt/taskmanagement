<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Models\Staff;
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
    public function index(Request $request): Response
    {
        $query = Staff::with(['roles'])->withCount('tasks');

        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
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

        $staff = $query->latest()->paginate(10)->withQueryString();
        $departments = Staff::distinct()->whereNotNull('department')->pluck('department');

        return Inertia::render('Admin/Staff/Index', [
            'staff' => $staff,
            'departments' => $departments,
            'filters' => $request->only(['search', 'status', 'department']),
        ]);
    }

    /**
     * Show form for creating a new staff member.
     */
    public function create(): Response
    {
        $roles = Role::where('guard_name', 'staff')->pluck('name');

        return Inertia::render('Admin/Staff/Create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created staff member.
     */
    public function store(StoreStaffRequest $request): RedirectResponse
    {
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

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member created successfully!');
    }

    /**
     * Show form for editing the staff member.
     */
    public function edit(Staff $staff): Response
    {
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
    public function update(UpdateStaffRequest $request, Staff $staff): RedirectResponse
    {
        $validated = $request->validated();
        $role = $validated['role'] ?? null;
        unset($validated['role']);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active');
        $validated['email_verified_at'] = $request->boolean('is_verified') ? ($staff->email_verified_at ?? now()) : null;

        $staff->update($validated);

        if ($role) {
            $staff->syncRoles([$role]);
        }

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member updated successfully!');
    }

    /**
     * Remove the specified staff member.
     */
    public function destroy(Staff $staff): RedirectResponse
    {
        $staff->delete();

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member deleted successfully.');
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(Staff $staff): RedirectResponse
    {
        $staff->is_active = ! $staff->is_active;
        $staff->save();

        $statusStr = $staff->is_active ? 'Activated' : 'Deactivated';
        return back()->with('success', "Staff account {$staff->name} has been {$statusStr}.");
    }

    /**
     * Toggle email verification.
     */
    public function toggleVerification(Staff $staff): RedirectResponse
    {
        $staff->email_verified_at = $staff->email_verified_at ? null : now();
        $staff->save();

        $statusStr = $staff->isVerified() ? 'Verified' : 'Unverified';
        return back()->with('success', "Staff email for {$staff->name} is now marked as {$statusStr}.");
    }
}
