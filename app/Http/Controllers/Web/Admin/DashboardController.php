<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Staff;
use App\Models\Task;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Admin Dashboard.
     */
    public function index(): View
    {
        $stats = [
            'total_projects' => Project::count(),
            'active_projects' => Project::whereIn('status', ['pending', 'in_progress'])->count(),
            'total_staff' => Staff::count(),
            'active_staff' => Staff::where('is_active', true)->count(),
            'total_tasks' => Task::count(),
            'pending_tasks' => Task::where('status', 'pending')->count(),
            'in_progress_tasks' => Task::where('status', 'in_progress')->count(),
            'completed_tasks' => Task::where('status', 'completed')->count(),
            'overdue_tasks' => Task::where('status', '!=', 'completed')
                ->whereNotNull('due_date')
                ->where('due_date', '<', now()->toDateString())
                ->count(),
        ];

        $recentTasks = Task::with(['project', 'assignedStaff', 'creator'])
            ->latest()
            ->take(6)
            ->get();

        $recentProjects = Project::withCount('tasks')
            ->latest()
            ->take(4)
            ->get();

        $staffMembers = Staff::withCount('tasks')
            ->take(5)
            ->get();

        $firstOverdueTask = Task::where('status', '!=', 'completed')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now()->toDateString())
            ->first();

        return view('admin.dashboard', compact('stats', 'recentTasks', 'recentProjects', 'staffMembers', 'firstOverdueTask'));
    }
}
