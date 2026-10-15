@extends('layouts.admin')

@section('title', 'Manage Projects - TaskFlow')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Projects</h1>
        <p class="page-subtitle">Track, monitor, and deliver organizational initiatives.</p>
    </div>
    <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Create Project
    </a>
</div>

<!-- Filters -->
<form method="GET" action="{{ route('admin.projects.index') }}" class="filter-bar">
    <div style="flex: 1; min-width: 200px;">
        <input type="text" name="search" class="form-control" value="{{ request('search') }}">
    </div>

    <div style="min-width: 160px;">
        <select name="status" class="form-control form-select">
            <option value="">All Statuses</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
        </select>
    </div>

    <button type="submit" class="btn btn-secondary">
        <i class="fa-solid fa-filter"></i> Filter
    </button>

    @if(request()->hasAny(['search', 'status']))
        <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary btn-sm" title="Clear filters">
            <i class="fa-solid fa-xmark"></i> Clear
        </a>
    @endif
</form>

<!-- Projects Cards Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.5rem;">
    @forelse($projects as $project)
        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; margin-bottom: 0;">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                    <span class="badge badge-{{ $project->status }}">
                        <span class="badge-dot"></span> {{ str_replace('_', ' ', $project->status) }}
                    </span>
                    <span style="font-size: 0.8rem; color: var(--text-muted);">
                        <i class="fa-solid fa-list-check"></i> {{ $project->tasks_count }} tasks
                    </span>
                </div>

                <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 0.5rem;">
                    <a href="{{ route('admin.projects.show', $project) }}" style="color: var(--text-primary);">
                        {{ $project->name }}
                    </a>
                </h3>

                <p style="color: var(--text-secondary); font-size: 0.875rem; line-height: 1.5; margin-bottom: 1.25rem;">
                    {{ Str::limit($project->description, 110) }}
                </p>
            </div>

            <div>
                <!-- Progress -->
                <div style="margin-bottom: 1.25rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 0.35rem;">
                        <span style="color: var(--text-muted);">Completion</span>
                        <span style="font-weight: 700; color: var(--brand-primary);">{{ $project->progress }}%</span>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width: {{ $project->progress }}%;"></div>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid var(--border-subtle); font-size: 0.8rem; color: var(--text-muted);">
                    <div>
                        @if($project->end_date)
                            <i class="fa-regular fa-calendar" style="margin-right: 4px;"></i> Due {{ $project->end_date->format('M d, Y') }}
                        @else
                            <span>No deadline set</span>
                        @endif
                    </div>

                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('admin.projects.show', $project) }}" class="btn btn-secondary btn-sm" title="View details">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-secondary btn-sm" title="Edit project">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Delete this project and all its tasks?');" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete project">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
            <div style="font-size: 2.5rem; color: var(--text-muted); margin-bottom: 1rem;">
                <i class="fa-regular fa-folder-open"></i>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--text-primary);">No projects found</h3>
            <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">Create a project to start assigning and managing tasks.</p>
            <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Create New Project
            </a>
        </div>
    @endforelse
</div>

<div style="margin-top: 2rem;">
    {{ $projects->links() }}
</div>
@endsection
