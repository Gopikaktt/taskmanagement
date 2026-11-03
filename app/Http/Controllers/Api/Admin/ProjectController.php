<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
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
    public function index(Request $request): JsonResponse|Response
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

        $projects = $query->latest()->paginate($request->input('per_page', 9))->withQueryString();

        if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
            return response()->json([
                'success' => true,
                'data' => ProjectResource::collection($projects),
                'meta' => [
                    'current_page' => $projects->currentPage(),
                    'last_page' => $projects->lastPage(),
                    'per_page' => $projects->perPage(),
                    'total' => $projects->total(),
                ],
            ]);
        }

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
    public function store(StoreProjectRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();
        $validated['created_by'] = Auth::guard('admin')->id() ?? $request->user()?->id;

        $project = Project::create($validated);

        if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
            return response()->json([
                'success' => true,
                'message' => 'Project created successfully.',
                'data' => new ProjectResource($project->load('creator')->loadCount('tasks')),
            ], 201);
        }

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'Project created successfully!');
    }

    /**
     * Display the specified project.
     */
    public function show(Request $request, Project $project): JsonResponse|Response
    {
        $project->load(['creator', 'tasks.assignedStaff', 'tasks.creator'])->loadCount('tasks');

        if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
            return response()->json([
                'success' => true,
                'data' => new ProjectResource($project),
            ]);
        }

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
    public function update(UpdateProjectRequest $request, Project $project): JsonResponse|RedirectResponse
    {
        $project->update($request->validated());

        if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
            return response()->json([
                'success' => true,
                'message' => 'Project updated successfully.',
                'data' => new ProjectResource($project->fresh(['creator'])->loadCount('tasks')),
            ]);
        }

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'Project updated successfully!');
    }

    /**
     * Remove the specified project.
     */
    public function destroy(Request $request, Project $project): JsonResponse|RedirectResponse
    {
        $project->delete();

        if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
            return response()->json([
                'success' => true,
                'message' => 'Project deleted successfully.',
            ]);
        }

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
