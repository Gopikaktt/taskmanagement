<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Http\Resources\StaffResource;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    /**
     * Display a listing of staff members.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Staff::withCount('tasks');

        if ($request->has('is_active')) {
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

        $staff = $query->latest()->paginate($request->input('per_page', 15));

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

    /**
     * Store a newly created staff member.
     */
    public function store(StoreStaffRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        
        if (isset($validated['is_verified']) && $validated['is_verified']) {
            $validated['email_verified_at'] = now();
        } else {
            $validated['email_verified_at'] = now(); // default verified unless specified
        }

        $staff = Staff::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Staff member created successfully.',
            'data' => new StaffResource($staff->loadCount('tasks')),
        ], 201);
    }

    /**
     * Display the specified staff member.
     */
    public function show(Staff $staff): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new StaffResource($staff->load('tasks.project')->loadCount('tasks')),
        ]);
    }

    /**
     * Update the specified staff member.
     */
    public function update(UpdateStaffRequest $request, Staff $staff): JsonResponse
    {
        $validated = $request->validated();

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

        return response()->json([
            'success' => true,
            'message' => 'Staff member updated successfully.',
            'data' => new StaffResource($staff->fresh()->loadCount('tasks')),
        ]);
    }

    /**
     * Remove the specified staff member.
     */
    public function destroy(Staff $staff): JsonResponse
    {
        $staff->delete();

        return response()->json([
            'success' => true,
            'message' => 'Staff member deleted successfully.',
        ]);
    }

    /**
     * Toggle active status of staff account.
     */
    public function toggleStatus(Staff $staff): JsonResponse
    {
        $staff->is_active = ! $staff->is_active;
        $staff->save();

        return response()->json([
            'success' => true,
            'message' => "Staff account is now " . ($staff->is_active ? 'Active' : 'Deactivated') . ".",
            'data' => new StaffResource($staff),
        ]);
    }

    /**
     * Toggle email verification status of staff account.
     */
    public function toggleVerification(Staff $staff): JsonResponse
    {
        $staff->email_verified_at = $staff->email_verified_at ? null : now();
        $staff->save();

        return response()->json([
            'success' => true,
            'message' => "Staff email verification is now " . ($staff->isVerified() ? 'Verified' : 'Unverified') . ".",
            'data' => new StaffResource($staff),
        ]);
    }
}
