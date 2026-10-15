<?php

namespace App\Http\Controllers\Web\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Display a listing of assigned tasks.
     */
    public function index(Request $request): View
    {
        $staff = Auth::guard('staff')->user();

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

        $tasks = $query->latest()->paginate(10)->withQueryString();

        return view('staff.tasks.index', compact('tasks'));
    }

    /**
     * Display the specified assigned task.
     */
    public function show(Task $task): View
    {
        $staff = Auth::guard('staff')->user();

        // Enforce staff ownership
        if ($task->staff_id !== $staff->id) {
            abort(403, 'Unauthorized: You are not assigned to this task.');
        }

        $task->load(['project', 'creator']);

        return view('staff.tasks.show', compact('task'));
    }

    /**
     * Update the status of the assigned task.
     */
    public function updateStatus(UpdateTaskStatusRequest $request, Task $task): RedirectResponse
    {
        $staff = Auth::guard('staff')->user();

        // Enforce staff ownership
        if ($task->staff_id !== $staff->id) {
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

        return back()->with('success', 'Task status successfully updated to ' . ucfirst(str_replace('_', ' ', $status)) . '!');
    }
}
