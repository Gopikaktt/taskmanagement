@extends('layouts.staff')

@section('title', 'Create Task - Staff Portal')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Create New Task</h1>
        <p class="page-subtitle">Add a new task item under your active projects.</p>
    </div>
    <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

<div class="card" style="max-width: 700px; margin: 0 auto;">
    <form action="{{ route('staff.tasks.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="title" class="form-label">Task Title <span style="color: #ef4444;">*</span></label>
            <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}" required placeholder="e.g. Implement authentication flows">
            @error('title')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="project_id" class="form-label">Project <span style="color: #ef4444;">*</span></label>
                <select id="project_id" name="project_id" class="form-control form-select" required>
                    <option value="">-- Select Project --</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
                @error('project_id')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="priority" class="form-label">Priority <span style="color: #ef4444;">*</span></label>
                <select id="priority" name="priority" class="form-control form-select" required>
                    <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
                @error('priority')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="due_date" class="form-label">Target Due Date</label>
            <input type="date" id="due_date" name="due_date" class="form-control" value="{{ old('due_date') }}">
            @error('due_date')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Task Instructions / Description</label>
            <textarea id="description" name="description" class="form-control" rows="4" placeholder="Detail the task deliverables and scope...">{{ old('description') }}</textarea>
            @error('description')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
            <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <i class="fa-solid fa-plus"></i> Create Task
            </button>
        </div>
    </form>
</div>
@endsection
