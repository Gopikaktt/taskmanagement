@extends('layouts.admin')

@section('title', 'Create Task - TaskFlow')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Create &amp; Assign Task</h1>
        <p class="page-subtitle">Schedule work item, define priorities, and allocate to active staff.</p>
    </div>
    <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Back to Tasks
    </a>
</div>

<div class="card" style="max-width: 750px; margin: 0 auto;">
    <form action="{{ route('admin.tasks.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="title" class="form-label">Task Title <span style="color: #ef4444;">*</span></label>
            <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}" required>
            @error('title')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="project_id" class="form-label">Associated Project <span style="color: #ef4444;">*</span></label>
                <select id="project_id" name="project_id" class="form-control form-select" required>
                    <option value="">-- Select Project --</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id', $selectedProjectId) == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
                @error('project_id')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="staff_id" class="form-label">Assign to Staff Member</label>
                <select id="staff_id" name="staff_id" class="form-control form-select">
                    <option value="">-- Unassigned (Allocate Later) --</option>
                    @foreach($staffMembers as $member)
                        <option value="{{ $member->id }}" {{ old('staff_id') == $member->id ? 'selected' : '' }}>
                            {{ $member->name }} ({{ $member->department ?? 'General' }})
                        </option>
                    @endforeach
                </select>
                @error('staff_id')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Task Description</label>
            <textarea id="description" name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
            @error('description')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="priority" class="form-label">Priority <span style="color: #ef4444;">*</span></label>
                <select id="priority" name="priority" class="form-control form-select" required>
                    <option value="low" {{ old('priority', 'medium') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ old('priority', 'medium') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="urgent" {{ old('priority', 'medium') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
                @error('priority')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Initial Status <span style="color: #ef4444;">*</span></label>
                <select id="status" name="status" class="form-control form-select" required>
                    <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ old('status', 'pending') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ old('status', 'pending') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ old('status', 'pending') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                @error('status')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="due_date" class="form-label">Due Date</label>
                <input type="date" id="due_date" name="due_date" class="form-control" value="{{ old('due_date') }}">
                @error('due_date')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-subtle);">
            <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Create Task
            </button>
        </div>
    </form>
</div>
@endsection
