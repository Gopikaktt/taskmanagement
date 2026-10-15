@extends('layouts.staff')

@section('title', 'Staff Portal - TaskFlow')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Welcome, {{ $staff->name }}</h1>
        <p class="page-subtitle">Track your assigned assignments, update progress, and meet project deliverables.</p>
    </div>
    <div style="display: flex; align-items: center; gap: 0.5rem;">
        <span class="badge badge-active">
            <i class="fa-solid fa-circle-check"></i> Active &amp; Verified Account
        </span>
    </div>
</div>

<!-- KPI Stats Grid -->
<div class="stats-grid">
    <a href="{{ route('staff.tasks.index') }}" class="stat-card" title="View all your tasks">
        <div class="stat-label">Total Assigned</div>
        <div class="stat-value">{{ $stats['total_assigned'] }}</div>
        <div class="stat-sub">Tasks assigned to you</div>
    </a>

    <a href="{{ route('staff.tasks.index', ['status' => 'in_progress']) }}" class="stat-card sky" title="View tasks in progress">
        <div class="stat-label">In Progress</div>
        <div class="stat-value" style="color: #0284c7;">{{ $stats['in_progress'] }}</div>
        <div class="stat-sub">Currently working on</div>
    </a>

    <a href="{{ route('staff.tasks.index', ['status' => 'completed']) }}" class="stat-card emerald" title="View completed tasks">
        <div class="stat-label">Completed</div>
        <div class="stat-value" style="color: #059669;">{{ $stats['completed'] }}</div>
        <div class="stat-sub">Delivered tasks</div>
    </a>

    <a href="{{ route('staff.tasks.index') }}" class="stat-card rose" title="View tasks">
        <div class="stat-label">Overdue Tasks</div>
        <div class="stat-value" style="color: {{ $stats['overdue'] > 0 ? '#b91c1c' : 'var(--text-primary)' }};">
            {{ $stats['overdue'] }}
        </div>
        <div class="stat-sub">{{ $stats['pending'] }} pending start</div>
    </a>
</div>

<!-- Recent Tasks Assigned to Me -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-list-check" style="margin-right: 6px; color: #059669;"></i> Your Active Tasks
        </div>
        <a href="{{ route('staff.tasks.index') }}" class="btn btn-secondary btn-sm">View All Assigned Tasks &rarr;</a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Task Title</th>
                    <th>Project</th>
                    <th>Priority</th>
                    <th>Due Date</th>
                    <th>Current Status</th>
                    <th>Update Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTasks as $task)
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
                            <!-- Quick Status Update Form -->
                            <form action="{{ route('staff.tasks.update-status', $task) }}" method="POST" style="display: flex; align-items: center; gap: 0.4rem;">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-control form-select" style="padding: 0.3rem 1.8rem 0.3rem 0.6rem; font-size: 0.8rem; width: auto;" onchange="this.form.submit()">
                                    <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $task->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                <a href="{{ route('staff.tasks.show', $task) }}" class="btn btn-secondary btn-sm" title="View details">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2.5rem;">
                            No tasks currently assigned to you.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
