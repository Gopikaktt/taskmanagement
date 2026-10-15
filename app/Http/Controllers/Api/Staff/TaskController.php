<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks assigned to the authenticated staff member.
     */
    public function index(Request $request): JsonResponse
    {
        $staff = $request->user();

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
     * Display the specified assigned task.
     */
    public function show(Request $request, Task $task): JsonResponse
    {
        $staff = $request->user();

        // Enforce that staff can only view tasks assigned to them
        if ($task->staff_id !== $staff->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: This task is not assigned to you.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => new TaskResource($task->load(['project', 'creator', 'assignedStaff'])),
        ]);
    }

    /**
     * Update the status of the specified task.
     */
    public function updateStatus(UpdateTaskStatusRequest $request, Task $task): JsonResponse
    {
        $staff = $request->user();

        // Enforce that staff can only update tasks assigned to them
        if ($task->staff_id !== $staff->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: You can only update tasks assigned to you.',
            ], 403);
        }

        $status = $request->status;
        $task->status = $status;

        if ($status === 'completed') {
            $task->completed_at = now();
        } else {
            $task->completed_at = null;
        }

        $task->save();

        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully.',
            'data' => new TaskResource($task->fresh(['project', 'creator', 'assignedStaff'])),
        ]);
    }

    /**
     * Summary stats for staff portal.
     */
    public function stats(Request $request): JsonResponse
    {
        $staff = $request->user();

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
