<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StaffController extends Controller
{
    /**
     * Display a listing of staff members.
     */
    public function index(Request $request): View
    {
        $query = Staff::withCount('tasks');

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

        return view('admin.staff.index', compact('staff', 'departments'));
    }

    /**
     * Show form for creating a new staff member.
     */
    public function create(): View
    {
        return view('admin.staff.create');
    }

    /**
     * Store a newly created staff member.
     */
    public function store(StoreStaffRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['email_verified_at'] = $request->boolean('is_verified', true) ? now() : null;

        Staff::create($validated);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member created successfully!');
    }

    /**
     * Show form for editing the staff member.
     */
    public function edit(Staff $staff): View
    {
        return view('admin.staff.edit', compact('staff'));
    }

    /**
     * Update the specified staff member.
     */
    public function update(UpdateStaffRequest $request, Staff $staff): RedirectResponse
    {
        $validated = $request->validated();

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active');
        $validated['email_verified_at'] = $request->boolean('is_verified') ? ($staff->email_verified_at ?? now()) : null;

        $staff->update($validated);

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
