<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     */
    public function index(Request $request): Response
    {
        $query = Project::with(['creator'])->withCount('tasks');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $projects = $query->latest()->paginate(9)->withQueryString();

        return Inertia::render('Admin/Projects/Index', [
            'projects' => $projects,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show form for creating a new project.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Projects/Create');
    }

    /**
     * Store a newly created project.
     */
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['created_by'] = Auth::guard('admin')->id();

        $project = Project::create($validated);

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'Project created successfully!');
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project): Response
    {
        $project->load(['creator', 'tasks.assignedStaff']);
        $staffMembers = Staff::where('is_active', true)->orderBy('name')->get();

        return Inertia::render('Admin/Projects/Show', [
            'project' => $project,
            'staffMembers' => $staffMembers,
        ]);
    }

    /**
     * Show form for editing the project.
     */
    public function edit(Project $project): Response
    {
        return Inertia::render('Admin/Projects/Edit', [
            'project' => $project,
        ]);
    }

    /**
     * Update the specified project.
     */
    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $project->update($request->validated());

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'Project updated successfully!');
    }

    /**
     * Remove the specified project.
     */
    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
