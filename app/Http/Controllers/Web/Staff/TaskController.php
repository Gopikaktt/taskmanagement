<?php

namespace App\Http\Controllers\Web\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    /**
     * Display a listing of assigned tasks.
     */
    public function index(Request $request): Response
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
        $projects = Project::orderBy('name')->get();
        return Inertia::render('Staff/Tasks/Create', compact('projects'));
    }

    /**
     * Store a new task created by staff.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
        ]);

        $validated['staff_id'] = Auth::guard('staff')->id();
        $validated['status'] = 'pending';

        Task::create($validated);

        return redirect()->route('staff.tasks.index')->with('success', 'Task created successfully!');
    }

    /**
     * Display the specified assigned task.
     */
    public function show(Task $task): Response
    {
        $staff = Auth::guard('staff')->user();

        // Enforce staff ownership
        if ($task->staff_id !== $staff->id) {
            abort(403, 'Unauthorized: You are not assigned to this task.');
        }

        $task->load(['project', 'creator']);

        return Inertia::render('Staff/Tasks/Show', compact('task'));
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
