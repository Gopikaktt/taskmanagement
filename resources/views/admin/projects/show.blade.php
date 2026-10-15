@extends('layouts.admin')

@section('title', $project->name . ' - TaskFlow')

@section('content')
<div class="page-header">
    <div>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <a href="{{ route('admin.projects.index') }}" style="color: var(--text-muted); font-size: 0.9rem;">
                <i class="fa-solid fa-arrow-left"></i> Projects
            </a>
            <span style="color: var(--text-muted);">&bull;</span>
            <span class="badge badge-{{ $project->status }}">
                <span class="badge-dot"></span> {{ str_replace('_', ' ', $project->status) }}
            </span>
        </div>
        <h1 class="page-title">{{ $project->name }}</h1>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="{{ route('admin.tasks.create', ['project_id' => $project->id]) }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Add Task
        </a>
        <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-secondary">
            <i class="fa-solid fa-pen"></i> Edit Project
        </a>
    </div>
</div>

<!-- Project Overview Card -->
<div class="card" style="margin-bottom: 2rem;">
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; align-items: start;">
        <div>
            <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Scope &amp; Description</h3>
            <p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6;">
                {{ $project->description ?: 'No project description provided.' }}
            </p>
        </div>

        <div style="background: var(--bg-subtle); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 1.25rem;">
            <div style="margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 0.4rem;">
                    <span style="color: var(--text-muted);">Overall Progress</span>
                    <span style="font-weight: 700; color: var(--brand-primary);">{{ $project->progress }}%</span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width: {{ $project->progress }}%;"></div>
                </div>
            </div>

            <div style="font-size: 0.85rem; display: flex; flex-direction: column; gap: 0.5rem;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">Start Date:</span>
                    <span>{{ $project->start_date ? $project->start_date->format('M d, Y') : 'Not set' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">Target Deadline:</span>
                    <span style="color: {{ $project->end_date && $project->end_date->isPast() && $project->status !== 'completed' ? '#b91c1c' : 'inherit' }}; font-weight: 600;">
                        {{ $project->end_date ? $project->end_date->format('M d, Y') : 'Not set' }}
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">Created By:</span>
                    <span>{{ $project->creator->name ?? 'Admin' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Project Tasks List -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-list-check" style="margin-right: 6px; color: #818cf8;"></i> Project Tasks ({{ $project->tasks->count() }})
        </div>
        <a href="{{ route('admin.tasks.create', ['project_id' => $project->id]) }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i> New Task
        </a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Task Name</th>
                    <th>Assigned Staff</th>
                    <th>Priority</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($project->tasks as $task)
                    <tr>
                        <td>
                            <a href="{{ route('admin.tasks.show', $task) }}" style="font-weight: 600; color: var(--text-primary);">
                                {{ $task->title }}
                            </a>
                            @if($task->is_overdue)
                                <span style="font-size: 0.7rem; color: #b91c1c; display: block;">
                                    <i class="fa-solid fa-triangle-exclamation"></i> Overdue
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($task->assignedStaff)
                                <span class="user-badge" style="font-size: 0.8rem;">
                                    <i class="fa-solid fa-user"></i> {{ $task->assignedStaff->name }}
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
                            {{ $task->due_date ? $task->due_date->format('M d, Y') : '-' }}
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
                                <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-secondary btn-sm" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                            No tasks under this project yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
