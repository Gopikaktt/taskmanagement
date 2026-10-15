@extends('layouts.admin')

@section('title', 'Manage Tasks - TaskFlow')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Tasks Management</h1>
        <p class="page-subtitle">Assign work, schedule deadlines, and monitor completion progress.</p>
    </div>
    <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Create Task
    </a>
</div>

<!-- Filters -->
<form method="GET" action="{{ route('admin.tasks.index') }}" class="filter-bar">
    <div style="flex: 2; min-width: 200px;">
        <input type="text" name="search" class="form-control" value="{{ request('search') }}">
    </div>

    <div style="flex: 1; min-width: 150px;">
        <select name="project_id" class="form-control form-select">
            <option value="">All Projects</option>
            @foreach($projects as $proj)
                <option value="{{ $proj->id }}" {{ request('project_id') == $proj->id ? 'selected' : '' }}>{{ $proj->name }}</option>
            @endforeach
        </select>
    </div>

    <div style="flex: 1; min-width: 150px;">
        <select name="staff_id" class="form-control form-select">
            <option value="">All Staff</option>
            @foreach($staffMembers as $member)
                <option value="{{ $member->id }}" {{ request('staff_id') == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
            @endforeach
        </select>
    </div>

    <div style="min-width: 130px;">
        <select name="priority" class="form-control form-select">
            <option value="">All Priorities</option>
            <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
        </select>
    </div>

    <div style="min-width: 130px;">
        <select name="status" class="form-control form-select">
            <option value="">All Statuses</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
    </div>

    <button type="submit" class="btn btn-secondary">
        <i class="fa-solid fa-filter"></i> Filter
    </button>

    @if(request()->hasAny(['search', 'project_id', 'staff_id', 'priority', 'status', 'overdue']))
        @if(request('overdue'))
            <span class="badge badge-urgent" style="font-size: 0.775rem; padding: 0.35rem 0.65rem;">
                <i class="fa-solid fa-triangle-exclamation"></i> Overdue Tasks Only
            </span>
        @endif
        <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary btn-sm" title="Clear filters">
            <i class="fa-solid fa-xmark"></i> Clear
        </a>
    @endif
</form>

<!-- Tasks Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Task Title</th>
                    <th>Project</th>
                    <th>Assigned Staff</th>
                    <th>Priority</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                    <tr>
                        <td>
                            <a href="{{ route('admin.tasks.show', $task) }}" style="font-weight: 700; color: var(--text-primary);">
                                {{ $task->title }}
                            </a>
                            @if($task->is_overdue)
                                <span style="font-size: 0.725rem; color: #b91c1c; font-weight: 600; display: block; margin-top: 2px;">
                                    <i class="fa-solid fa-triangle-exclamation"></i> Overdue (Deadline: {{ $task->due_date->format('M d, Y') }})
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($task->project)
                                <a href="{{ route('admin.projects.show', $task->project) }}" style="color: var(--brand-primary); font-size: 0.85rem; font-weight: 600;">
                                    {{ $task->project->name }}
                                </a>
                            @else
                                <span style="color: var(--text-muted); font-size: 0.85rem;">None</span>
                            @endif
                        </td>
                        <td>
                            @if($task->assignedStaff)
                                <span class="user-badge" style="font-size: 0.8rem;">
                                    <i class="fa-solid fa-user-check" style="color: #15803d; font-size: 0.75rem;"></i> {{ $task->assignedStaff->name }}
                                </span>
                            @else
                                <span style="color: var(--text-muted); font-size: 0.8rem; font-style: italic;">Unassigned</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $task->priority }}">
                                <span class="badge-dot"></span> {{ $task->priority }}
                            </span>
                        </td>
                        <td>
                            <span style="font-size: 0.85rem; color: {{ $task->is_overdue ? '#b91c1c' : 'inherit' }}; font-weight: {{ $task->is_overdue ? '700' : 'normal' }};">
                                {{ $task->due_date ? $task->due_date->format('M d, Y') : '-' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $task->status }}">
                                <span class="badge-dot"></span> {{ str_replace('_', ' ', $task->status) }}
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.4rem;">
                                <a href="{{ route('admin.tasks.show', $task) }}" class="btn btn-secondary btn-sm" title="View details">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-secondary btn-sm" title="Edit task">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete task">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2.5rem;">
                            No tasks found matching your criteria. <a href="{{ route('admin.tasks.create') }}">Create one now</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: 2rem;">
    {{ $tasks->links() }}
</div>
@endsection
