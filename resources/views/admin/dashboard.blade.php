@extends('layouts.admin')

@section('title', 'Admin Dashboard - TaskFlow')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Executive Dashboard</h1>
        <p class="page-subtitle">Real-time oversight of projects, staff allocation, and task progress.</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="{{ route('admin.projects.create') }}" class="btn btn-secondary">
            <i class="fa-solid fa-folder-plus"></i> New Project
        </a>
        <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Create Task
        </a>
    </div>
</div>

<!-- KPI Stats Grid (Clickable Cards) -->
<div class="stats-grid">
    <a href="{{ route('admin.projects.index') }}" class="stat-card" title="View all projects">
        <div class="stat-label">Total Projects</div>
        <div class="stat-value">{{ $stats['total_projects'] }}</div>
        <div class="stat-sub"><span style="color: #0284c7; font-weight: 600;">{{ $stats['active_projects'] }}</span> in progress / active</div>
    </a>

    <a href="{{ route('admin.staff.index') }}" class="stat-card emerald" title="Manage staff members">
        <div class="stat-label">Staff Members</div>
        <div class="stat-value">{{ $stats['total_staff'] }}</div>
        <div class="stat-sub"><span style="color: #059669; font-weight: 600;">{{ $stats['active_staff'] }}</span> active &amp; verified</div>
    </a>

    <a href="{{ route('admin.tasks.index') }}" class="stat-card amber" title="View all tasks">
        <div class="stat-label">Total Tasks</div>
        <div class="stat-value">{{ $stats['total_tasks'] }}</div>
        <div class="stat-sub"><span style="color: #d97706; font-weight: 600;">{{ $stats['in_progress_tasks'] }}</span> in progress</div>
    </a>

    @php
        $overdueUrl = route('admin.tasks.index');
        if ($stats['overdue_tasks'] === 1 && isset($firstOverdueTask)) {
            $overdueUrl = route('admin.tasks.show', $firstOverdueTask);
        } elseif ($stats['overdue_tasks'] > 0) {
            $overdueUrl = route('admin.tasks.index', ['overdue' => 1]);
        }
    @endphp
    <a href="{{ $overdueUrl }}" class="stat-card rose" title="{{ $stats['overdue_tasks'] === 1 ? 'Go to overdue task' : 'View overdue tasks' }}">
        <div class="stat-label">Overdue Tasks</div>
        <div class="stat-value" style="color: {{ $stats['overdue_tasks'] > 0 ? '#b91c1c' : 'var(--text-primary)' }};">
            {{ $stats['overdue_tasks'] }}
        </div>
        <div class="stat-sub"><span style="color: #15803d; font-weight: 600;">{{ $stats['completed_tasks'] }}</span> completed</div>
    </a>
</div>

<!-- Two column layout: Recent Tasks + Project Progress -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <!-- Recent Tasks -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-list-check" style="margin-right: 6px; color: #4f46e5;"></i> Recent Tasks
            </div>
            <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary btn-sm">View All &rarr;</a>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Project</th>
                        <th>Assigned To</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTasks as $task)
                        <tr>
                            <td>
                                <a href="{{ route('admin.tasks.show', $task) }}" style="font-weight: 600; color: var(--text-primary);">
                                    {{ $task->title }}
                                </a>
                                @if($task->is_overdue)
                                    <span style="font-size: 0.7rem; color: #b91c1c; font-weight: 600; display: block;">
                                        <i class="fa-solid fa-circle-exclamation"></i> Overdue ({{ $task->due_date->format('M d') }})
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span style="color: var(--text-secondary); font-size: 0.85rem;">{{ $task->project->name ?? 'None' }}</span>
                            </td>
                            <td>
                                @if($task->assignedStaff)
                                    <span class="user-badge" style="font-size: 0.775rem;">
                                        <i class="fa-solid fa-user" style="font-size: 0.7rem;"></i> {{ $task->assignedStaff->name }}
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
                                <span class="badge badge-{{ $task->status }}">
                                    <span class="badge-dot"></span> {{ str_replace('_', ' ', $task->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-secondary btn-sm" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                No tasks created yet. <a href="{{ route('admin.tasks.create') }}">Create one now</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Active Projects & Staff Breakdown -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-folder-open" style="margin-right: 6px; color: #0284c7;"></i> Active Projects
                </div>
                <a href="{{ route('admin.projects.index') }}" style="font-size: 0.8rem;">View All</a>
            </div>

            @forelse($recentProjects as $project)
                <div style="margin-bottom: 1.25rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                        <a href="{{ route('admin.projects.show', $project) }}" style="font-weight: 600; font-size: 0.9rem; color: var(--text-primary);">
                            {{ $project->name }}
                        </a>
                        <span style="font-size: 0.8rem; font-weight: 700; color: var(--brand-primary);">{{ $project->progress }}%</span>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width: {{ $project->progress }}%;"></div>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--text-muted); margin-top: 0.35rem;">
                        <span>{{ $project->tasks_count }} Tasks</span>
                        <span class="badge badge-{{ $project->status }}" style="font-size: 0.65rem; padding: 1px 6px;">
                            {{ str_replace('_', ' ', $project->status) }}
                        </span>
                    </div>
                </div>
            @empty
                <div style="color: var(--text-muted); text-align: center; font-size: 0.85rem; padding: 1rem;">No projects found.</div>
            @endforelse
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-users-gear" style="margin-right: 6px; color: #059669;"></i> Team Staff
                </div>
                <a href="{{ route('admin.staff.index') }}" style="font-size: 0.8rem;">Manage Staff</a>
            </div>

            @foreach($staffMembers as $staff)
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.6rem 0; border-bottom: 1px solid var(--border-subtle);">
                    <div>
                        <div style="font-weight: 600; font-size: 0.875rem; color: var(--text-primary);">{{ $staff->name }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $staff->department ?? 'General' }}</div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span class="badge {{ $staff->is_active ? 'badge-active' : 'badge-inactive' }}" style="font-size: 0.65rem;">
                            {{ $staff->is_active ? 'Active' : 'Deactivated' }}
                        </span>
                        <span style="font-size: 0.8rem; color: var(--text-secondary);">{{ $staff->tasks_count }} tasks</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
