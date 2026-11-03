<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Jobs\SendTaskAssignedNotification;
use App\Models\Project;
use App\Models\Staff;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks.
     */
    public function index(Request $request): JsonResponse|Response
    {
        $query = Task::with(['project', 'assignedStaff', 'creator']);

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('creator_type')) {
            if ($request->creator_type === 'admin') {
                $query->whereNotNull('created_by');
            } elseif ($request->creator_type === 'staff') {
                $query->whereNull('created_by');
            }
        }

        if ($request->boolean('overdue')) {
            $query->where('status', '!=', 'completed')
                  ->whereNotNull('due_date')
                  ->where('due_date', '<', now()->toDateString());
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tasks = $query->latest()->paginate($request->input('per_page', 10))->withQueryString();

        // If accessed as pure API endpoint:
        if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
            return response()->json([
                'success' => true,
                'data' => TaskResource::collection($tasks),
                'meta' => [
                    'current_page' => $tasks->currentPage(),
                    'last_page' => $tasks->lastPage(),
                    'per_page' => $tasks->perPage(),
                    'total' => $tasks->total(),
                ],
            ]);
        }

        // Web Inertia render:
        $projects = Project::orderBy('name')->get();
        $staffMembers = Staff::where('is_active', true)->orderBy('name')->get();

        return Inertia::render('Admin/Tasks/Index', [
            'tasks' => $tasks,
            'projects' => $projects,
            'staffMembers' => $staffMembers,
            'filters' => $request->only(['search', 'project_id', 'staff_id', 'priority', 'status', 'overdue', 'creator_type']),
        ]);
    }

    /**
     * Show the form for creating a new task.
     */
    public function create(Request $request): Response
    {
        $projects = Project::orderBy('name')->get();
        $staffMembers = Staff::where('is_active', true)->orderBy('name')->get();
        $selectedProjectId = $request->query('project_id');

        return Inertia::render('Admin/Tasks/Create', [
            'projects' => $projects,
            'staffMembers' => $staffMembers,
            'selectedProjectId' => $selectedProjectId,
        ]);
    }

    /**
     * Store a newly created task and dispatch notification if assigned.
     */
    public function store(StoreTaskRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();
        $validated['created_by'] = Auth::guard('admin')->id() ?? $request->user()?->id;

        if ($validated['status'] === 'completed') {
            $validated['completed_at'] = now();
        }

        $task = Task::create($validated);

        // Dispatch queued background notification if assigned to staff
        if ($task->staff_id) {
            $staff = Staff::find($task->staff_id);
            if ($staff) {
                SendTaskAssignedNotification::dispatch($task, $staff);
            }
        }

        if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
            return response()->json([
                'success' => true,
                'message' => 'Task assigned successfully.',
                'data' => new TaskResource($task->load(['project', 'assignedStaff', 'creator'])),
            ], 201);
        }

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task assigned successfully!');
    }

    /**
     * Display the specified task.
     */
    public function show(Request $request, Task $task): JsonResponse|Response
    {
        $task->load(['project', 'assignedStaff', 'creator']);

        if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
            return response()->json([
                'success' => true,
                'data' => new TaskResource($task),
            ]);
        }

        return Inertia::render('Admin/Tasks/Show', [
            'task' => $task,
        ]);
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task): Response
    {
        $projects = Project::orderBy('name')->get();
        $staffMembers = Staff::where('is_active', true)->orderBy('name')->get();
        $task->load(['project', 'assignedStaff']);

        return Inertia::render('Admin/Tasks/Edit', [
            'task' => $task,
            'projects' => $projects,
            'staffMembers' => $staffMembers,
        ]);
    }

    /**
     * Update the specified task and dispatch notification if reassigned.
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse|RedirectResponse
    {
        $oldStaffId = $task->staff_id;
        $validated = $request->validated();

        if (isset($validated['status'])) {
            if ($validated['status'] === 'completed' && $task->status !== 'completed') {
                $validated['completed_at'] = now();
            } elseif ($validated['status'] !== 'completed') {
                $validated['completed_at'] = null;
            }
        }

        $task->update($validated);

        // Check if newly assigned or assigned to another staff member
        if ($task->staff_id && $task->staff_id != $oldStaffId) {
            $staff = Staff::find($task->staff_id);
            if ($staff) {
                SendTaskAssignedNotification::dispatch($task, $staff);
            }
        }

        if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully.',
                'data' => new TaskResource($task->fresh(['project', 'assignedStaff', 'creator'])),
            ]);
        }

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task updated successfully!');
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Request $request, Task $task): JsonResponse|RedirectResponse
    {
        $task->delete();

        if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
            return response()->json([
                'success' => true,
                'message' => 'Task deleted successfully.',
            ]);
        }

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
}
