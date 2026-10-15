@extends('layouts.staff')

@section('title', 'My Assigned Tasks - TaskFlow')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">My Assigned Tasks</h1>
        <p class="page-subtitle">Tasks assigned to you by administrators. Review requirements and update status as you work.</p>
    </div>
</div>

<!-- Filters -->
<form method="GET" action="{{ route('staff.tasks.index') }}" class="filter-bar">
    <div style="flex: 2; min-width: 200px;">
        <input type="text" name="search" class="form-control" value="{{ request('search') }}">
    </div>

    <div style="min-width: 150px;">
        <select name="status" class="form-control form-select">
            <option value="">All Statuses</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
    </div>

    <div style="min-width: 150px;">
        <select name="priority" class="form-control form-select">
            <option value="">All Priorities</option>
            <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
        </select>
    </div>

    <button type="submit" class="btn btn-secondary">
        <i class="fa-solid fa-filter"></i> Filter
    </button>

    @if(request()->hasAny(['search', 'status', 'priority']))
        <a href="{{ route('staff.tasks.index') }}" class="btn btn-secondary btn-sm" title="Clear filters">
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
                    <th>Task Name</th>
                    <th>Project</th>
                    <th>Priority</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Quick Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                    <tr>
                        <td>
                            <a href="{{ route('staff.tasks.show', $task) }}" style="font-weight: 700; color: var(--text-primary);">
                                {{ $task->title }}
                            </a>
                            @if($task->is_overdue)
                                <span style="font-size: 0.725rem; color: #b91c1c; font-weight: 600; display: block; margin-top: 2px;">
                                    <i class="fa-solid fa-triangle-exclamation"></i> Overdue ({{ $task->due_date->format('M d, Y') }})
                                </span>
                            @endif
                        </td>
                        <td>
                            <span style="color: var(--text-secondary); font-size: 0.85rem;">
                                {{ $task->project->name ?? 'None' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $task->priority }}">
                                <span class="badge-dot"></span> {{ $task->priority }}
                            </span>
                        </td>
                        <td>
                            <span style="font-size: 0.85rem; color: {{ $task->is_overdue ? '#b91c1c' : 'inherit' }}; font-weight: {{ $task->is_overdue ? '700' : 'normal' }};">
                                {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No deadline' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $task->status }}">
                                <span class="badge-dot"></span> {{ str_replace('_', ' ', $task->status) }}
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('staff.tasks.update-status', $task) }}" method="POST" style="display: flex; align-items: center; gap: 0.4rem;">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-control form-select" style="padding: 0.35rem 1.8rem 0.35rem 0.65rem; font-size: 0.825rem; width: auto;" onchange="this.form.submit()">
                                    <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $task->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                <a href="{{ route('staff.tasks.show', $task) }}" class="btn btn-secondary btn-sm" title="View details">
                                    <i class="fa-solid fa-eye"></i> Details
                                </a>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 3rem;">
                            No assigned tasks match your search filter.
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
