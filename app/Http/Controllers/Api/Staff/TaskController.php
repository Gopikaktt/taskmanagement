<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
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
     * Display a listing of tasks assigned to the authenticated staff member.
     */
    public function index(Request $request): JsonResponse|Response
    {
        $staff = $request->user() ?? Auth::guard('staff')->user();

        $query = Task::where('staff_id', $staff->id)
            ->with(['project', 'creator']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
            $tasks = $query->latest()->paginate($request->input('per_page', 15));

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

        $tasks = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Staff/Tasks/Index', [
            'tasks' => $tasks,
            'filters' => $request->only(['search', 'status', 'priority']),
        ]);
    }

    /**
     * Show form for creating a task (staff with 'create tasks' permission).
     */
    public function create(): Response
    {
        $staff = Auth::guard('staff')->user();

        if (! $staff || ! $staff->can('create tasks')) {
            abort(403, 'Unauthorized: You do not have permission to create tasks.');
        }

        $projects = Project::orderBy('name')->get();
        return Inertia::render('Staff/Tasks/Create', compact('projects'));
    }

    /**
     * Store a new task created by staff.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $staff = $request->user() ?? Auth::guard('staff')->user();

        if (! $staff || ! $staff->can('create tasks')) {
            if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: You do not have permission to create tasks.',
                ], 403);
            }

            abort(403, 'Unauthorized: You do not have permission to create tasks.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
        ]);

        $validated['staff_id'] = $staff->id;
        $validated['status'] = 'pending';

        $task = Task::create($validated);

        if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
            return response()->json([
                'success' => true,
                'message' => 'Task created successfully.',
                'data' => new TaskResource($task->load(['project', 'creator', 'assignedStaff'])),
            ], 201);
        }

        return redirect()->route('staff.tasks.index')->with('success', 'Task created successfully!');
    }

    /**
     * Display the specified assigned task.
     */
    public function show(Request $request, Task $task): JsonResponse|Response
    {
        $staff = $request->user() ?? Auth::guard('staff')->user();

        // Enforce that staff can only view tasks assigned to them
        if ($task->staff_id !== $staff->id) {
            if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: This task is not assigned to you.',
                ], 403);
            }

            abort(403, 'Unauthorized: You are not assigned to this task.');
        }

        if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
            return response()->json([
                'success' => true,
                'data' => new TaskResource($task->load(['project', 'creator', 'assignedStaff'])),
            ]);
        }

        $task->load(['project', 'creator']);

        return Inertia::render('Staff/Tasks/Show', compact('task'));
    }

    /**
     * Update the status of the specified task.
     */
    public function updateStatus(UpdateTaskStatusRequest $request, Task $task): JsonResponse|RedirectResponse
    {
        $staff = $request->user() ?? Auth::guard('staff')->user();

        // Enforce that staff can only update tasks assigned to them
        if ($task->staff_id !== $staff->id) {
            if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: You can only update tasks assigned to you.',
                ], 403);
            }

            abort(403, 'Unauthorized: You can only update tasks assigned to you.');
        }

        $status = $request->status;
        $task->status = $status;

        if ($status === 'completed') {
            $task->completed_at = now();
        } else {
            $task->completed_at = null;
        }

        $task->save();

        if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
            return response()->json([
                'success' => true,
                'message' => 'Task status updated successfully.',
                'data' => new TaskResource($task->fresh(['project', 'creator', 'assignedStaff'])),
            ]);
        }

        return back()->with('success', 'Task status successfully updated to ' . ucfirst(str_replace('_', ' ', $status)) . '!');
    }

    /**
     * Summary stats for staff portal.
     */
    public function stats(Request $request): JsonResponse
    {
        $staff = $request->user() ?? Auth::guard('staff')->user();

        $totalAssigned = Task::where('staff_id', $staff->id)->count();
        $pending = Task::where('staff_id', $staff->id)->where('status', 'pending')->count();
        $inProgress = Task::where('staff_id', $staff->id)->where('status', 'in_progress')->count();
        $completed = Task::where('staff_id', $staff->id)->where('status', 'completed')->count();
        $overdue = Task::where('staff_id', $staff->id)
            ->where('status', '!=', 'completed')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now()->toDateString())
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_assigned' => $totalAssigned,
                'pending' => $pending,
                'in_progress' => $inProgress,
                'completed' => $completed,
                'overdue' => $overdue,
            ],
        ]);
    }
}
