@extends('layouts.admin')

@section('title', $task->title . ' - TaskFlow')

@section('content')
<div class="page-header">
    <div>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <a href="{{ route('admin.tasks.index') }}" style="color: var(--text-muted); font-size: 0.9rem;">
                <i class="fa-solid fa-arrow-left"></i> Tasks
            </a>
            <span style="color: var(--text-muted);">&bull;</span>
            <span class="badge badge-{{ $task->priority }}">{{ $task->priority }}</span>
            <span class="badge badge-{{ $task->status }}">{{ str_replace('_', ' ', $task->status) }}</span>
        </div>
        <h1 class="page-title">{{ $task->title }}</h1>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-secondary">
            <i class="fa-solid fa-pen"></i> Edit Task
        </a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    <div>
        <div class="card">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.75rem;">Description &amp; Specifications</h3>
            <p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.7; white-space: pre-line;">
                {{ $task->description ?: 'No detailed description provided for this task.' }}
            </p>
        </div>
    </div>

    <div>
        <div class="card">
            <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.75rem;">
                Task Metadata
            </h3>

            <div style="font-size: 0.875rem; display: flex; flex-direction: column; gap: 1rem;">
                <div>
                    <div style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Project</div>
                    <div style="font-weight: 600; margin-top: 2px;">
                        <a href="{{ route('admin.projects.show', $task->project) }}" style="color: var(--brand-primary);">
                            {{ $task->project->name }}
                        </a>
                    </div>
                </div>

                <div>
                    <div style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Assigned Staff</div>
                    <div style="margin-top: 4px;">
                        @if($task->assignedStaff)
                            <div class="user-badge">
                                <i class="fa-solid fa-user-check" style="color: #15803d;"></i>
                                <span>{{ $task->assignedStaff->name }}</span>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">{{ $task->assignedStaff->email }}</div>
                        @else
                            <span style="color: var(--text-muted); font-style: italic;">Unassigned</span>
                        @endif
                    </div>
                </div>

                <div>
                    <div style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Due Date</div>
                    <div style="font-weight: 600; margin-top: 2px; color: {{ $task->is_overdue ? '#b91c1c' : 'var(--text-primary)' }};">
                        {{ $task->due_date ? $task->due_date->format('F d, Y') : 'No deadline' }}
                        @if($task->is_overdue)
                            <span class="badge badge-urgent" style="margin-left: 6px;">Overdue</span>
                        @endif
                    </div>
                </div>

                <div>
                    <div style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Completed At</div>
                    <div style="margin-top: 2px;">
                        {{ $task->completed_at ? $task->completed_at->format('F d, Y H:i') : 'Incomplete' }}
                    </div>
                </div>

                <div>
                    <div style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Created By</div>
                    <div style="margin-top: 2px;">
                        {{ $task->creator->name ?? 'Administrator' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
