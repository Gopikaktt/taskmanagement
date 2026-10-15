<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Jobs\SendTaskAssignedNotification;
use App\Models\Project;
use App\Models\Staff;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks.
     */
    public function index(Request $request): View
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

        $tasks = $query->latest()->paginate(10)->withQueryString();
        $projects = Project::orderBy('name')->get();
        $staffMembers = Staff::where('is_active', true)->orderBy('name')->get();

        return view('admin.tasks.index', compact('tasks', 'projects', 'staffMembers'));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create(Request $request): View
    {
        $projects = Project::orderBy('name')->get();
        $staffMembers = Staff::where('is_active', true)->orderBy('name')->get();
        $selectedProjectId = $request->input('project_id');

        return view('admin.tasks.create', compact('projects', 'staffMembers', 'selectedProjectId'));
    }

    /**
     * Store a newly created task and dispatch notification.
     */
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['created_by'] = Auth::guard('admin')->id();

        if ($validated['status'] === 'completed') {
            $validated['completed_at'] = now();
        }

        $task = Task::create($validated);

        // Queue background notification to staff member
        if ($task->staff_id) {
            $staff = Staff::find($task->staff_id);
            if ($staff) {
                SendTaskAssignedNotification::dispatch($task, $staff);
            }
        }

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task assigned successfully!');
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task): View
    {
        $task->load(['project', 'assignedStaff', 'creator']);
        return view('admin.tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the task.
     */
    public function edit(Task $task): View
    {
        $projects = Project::orderBy('name')->get();
        $staffMembers = Staff::where('is_active', true)->orderBy('name')->get();

        return view('admin.tasks.edit', compact('task', 'projects', 'staffMembers'));
    }

    /**
     * Update the specified task and dispatch notification if reassigned.
     */
    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
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

        // Check if assigned to a new or different staff member
        if ($task->staff_id && $task->staff_id != $oldStaffId) {
            $staff = Staff::find($task->staff_id);
            if ($staff) {
                SendTaskAssignedNotification::dispatch($task, $staff);
            }

            return redirect()->route('admin.tasks.index')
                ->with('success', 'Task assigned successfully!');
        }

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task updated successfully!');
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
}
