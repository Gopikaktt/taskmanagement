@extends('layouts.staff')

@section('title', $task->title . ' - Staff Portal')

@section('content')
<div class="page-header">
    <div>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <a href="{{ route('staff.tasks.index') }}" style="color: var(--text-muted); font-size: 0.9rem;">
                <i class="fa-solid fa-arrow-left"></i> My Tasks
            </a>
            <span style="color: var(--text-muted);">&bull;</span>
            <span class="badge badge-{{ $task->priority }}">{{ $task->priority }}</span>
            <span class="badge badge-{{ $task->status }}">{{ str_replace('_', ' ', $task->status) }}</span>
        </div>
        <h1 class="page-title">{{ $task->title }}</h1>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    <!-- Task Description -->
    <div>
        <div class="card">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.75rem;">Requirements &amp; Description</h3>
            <p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.7; white-space: pre-line;">
                {{ $task->description ?: 'No detailed instructions provided.' }}
            </p>
        </div>

        <!-- Project Context -->
        <div class="card">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                <i class="fa-solid fa-folder" style="color: #0284c7; margin-right: 6px;"></i> Project: {{ $task->project->name ?? 'General Project' }}
            </h3>
            <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6;">
                {{ $task->project->description ?? 'No project overview description provided.' }}
            </p>
        </div>
    </div>

    <!-- Status Update Action Card -->
    <div>
        <div class="card" style="border: 1px solid var(--border-subtle);">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.75rem;">
                Update Task Status
            </h3>

            <form action="{{ route('staff.tasks.update-status', $task) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label for="status" class="form-label">Change Status</label>
                    <select id="status" name="status" class="form-control form-select">
                        <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $task->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="fa-solid fa-check"></i> Save Status
                </button>
            </form>

            <div style="font-size: 0.85rem; display: flex; flex-direction: column; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-subtle);">
                <div>
                    <span style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Due Date</span>
                    <div style="font-weight: 600; color: {{ $task->is_overdue ? '#b91c1c' : 'var(--text-primary)' }};">
                        {{ $task->due_date ? $task->due_date->format('F d, Y') : 'No deadline' }}
                        @if($task->is_overdue)
                            <span class="badge badge-urgent" style="margin-left: 4px;">Overdue</span>
                        @endif
                    </div>
                </div>

                <div>
                    <span style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Assigned By</span>
                    <div style="font-weight: 600;">{{ $task->creator->name ?? 'Administrator' }}</div>
                </div>

                <div>
                    <span style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Completed At</span>
                    <div>{{ $task->completed_at ? $task->completed_at->format('M d, Y H:i') : 'Not yet completed' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
