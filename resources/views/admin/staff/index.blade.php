@extends('layouts.admin')

@section('title', 'Manage Staff Members - TaskFlow')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Staff Members</h1>
        <p class="page-subtitle">Manage personnel accounts, eligibility verification, and department assignments.</p>
    </div>
    <a href="{{ route('admin.staff.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-user-plus"></i> Add Staff Member
    </a>
</div>

<!-- Filters -->
<form method="GET" action="{{ route('admin.staff.index') }}" class="filter-bar">
    <div style="flex: 1; min-width: 200px;">
        <input type="text" name="search" class="form-control" value="{{ request('search') }}">
    </div>

    <div style="min-width: 150px;">
        <select name="status" class="form-control form-select">
            <option value="">All Account Statuses</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Deactivated Only</option>
        </select>
    </div>

    <div style="min-width: 160px;">
        <select name="department" class="form-control form-select">
            <option value="">All Departments</option>
            @foreach($departments as $dept)
                <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn btn-secondary">
        <i class="fa-solid fa-filter"></i> Filter
    </button>

    @if(request()->hasAny(['search', 'status', 'department']))
        <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary btn-sm" title="Clear filters">
            <i class="fa-solid fa-xmark"></i> Clear
        </a>
    @endif
</form>

<!-- Staff Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Staff Member</th>
                    <th>Department</th>
                    <th>Contact</th>
                    <th>Account Status</th>
                    <th>Email Verification</th>
                    <th>Assigned Tasks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($staff as $member)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div class="user-avatar">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: var(--text-primary);">{{ $member->name }}</div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $member->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="color: var(--text-secondary); font-size: 0.85rem;">
                                {{ $member->department ?: 'Unassigned' }}
                            </span>
                        </td>
                        <td>
                            <span style="color: var(--text-secondary); font-size: 0.85rem;">
                                {{ $member->phone ?: '-' }}
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('admin.staff.toggle-status', $member) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="badge {{ $member->is_active ? 'badge-active' : 'badge-inactive' }}" style="cursor: pointer; border-style: dashed;" title="Click to toggle account active status">
                                    <span class="badge-dot"></span> {{ $member->is_active ? 'Active' : 'Deactivated' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <form action="{{ route('admin.staff.toggle-verification', $member) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="badge {{ $member->isVerified() ? 'badge-verified' : 'badge-unverified' }}" style="cursor: pointer; border-style: dashed;" title="Click to toggle email verification status">
                                    <span class="badge-dot"></span> {{ $member->isVerified() ? 'Verified' : 'Unverified' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <a href="{{ route('admin.tasks.index', ['staff_id' => $member->id]) }}" style="font-weight: 600; color: var(--brand-primary);">
                                {{ $member->tasks_count }} Tasks
                            </a>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.4rem;">
                                <a href="{{ route('admin.staff.edit', $member) }}" class="btn btn-secondary btn-sm" title="Edit member">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('admin.staff.destroy', $member) }}" method="POST" onsubmit="return confirm('Delete this staff member?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete member">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2.5rem;">
                            No staff members match the criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: 2rem;">
    {{ $staff->links() }}
</div>
@endsection
