<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Jobs\SendTaskAssignedNotification;
use App\Models\Staff;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks.
     */
    public function index(Request $request): JsonResponse
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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

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

    /**
     * Store a newly created task and dispatch notification if assigned.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['created_by'] = $request->user()->id;

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

        return response()->json([
            'success' => true,
            'message' => 'Task assigned successfully.',
            'data' => new TaskResource($task->load(['project', 'assignedStaff', 'creator'])),
        ], 201);
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new TaskResource($task->load(['project', 'assignedStaff', 'creator'])),
        ]);
    }

    /**
     * Update the specified task and dispatch notification if reassigned.
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
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

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully.',
            'data' => new TaskResource($task->fresh(['project', 'assignedStaff', 'creator'])),
        ]);
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully.',
        ]);
    }
}
