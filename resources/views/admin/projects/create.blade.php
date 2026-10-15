@extends('layouts.admin')

@section('title', 'Create Project - TaskFlow')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Create New Project</h1>
        <p class="page-subtitle">Initiate a workspace for tasks and staff assignments.</p>
    </div>
    <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Back to Projects
    </a>
</div>

<div class="card" style="max-width: 700px; margin: 0 auto;">
    <form action="{{ route('admin.projects.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name" class="form-label">Project Name <span style="color: #ef4444;">*</span></label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
            @error('name')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control">{{ old('description') }}</textarea>
            @error('description')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="status" class="form-label">Initial Status <span style="color: #ef4444;">*</span></label>
                <select id="status" name="status" class="form-control form-select" required>
                    <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                </select>
                @error('status')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ old('start_date', date('Y-m-d')) }}">
                @error('start_date')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="end_date" class="form-label">End / Due Date</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                @error('end_date')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-subtle);">
            <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-check"></i> Create Project
            </button>
        </div>
    </form>
</div>
@endsection
