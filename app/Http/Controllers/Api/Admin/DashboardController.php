<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Staff;
use App\Models\Task;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Get aggregate analytics and summary statistics for Admin dashboard.
     */
    public function stats(): JsonResponse
    {
        $totalProjects = Project::count();
        $projectsByStatus = [
            'pending' => Project::where('status', 'pending')->count(),
            'in_progress' => Project::where('status', 'in_progress')->count(),
            'completed' => Project::where('status', 'completed')->count(),
            'on_hold' => Project::where('status', 'on_hold')->count(),
        ];

        $totalStaff = Staff::count();
        $activeStaff = Staff::where('is_active', true)->count();
        $inactiveStaff = Staff::where('is_active', false)->count();
        $verifiedStaff = Staff::whereNotNull('email_verified_at')->count();

        $totalTasks = Task::count();
        $tasksByStatus = [
            'pending' => Task::where('status', 'pending')->count(),
            'in_progress' => Task::where('status', 'in_progress')->count(),
            'completed' => Task::where('status', 'completed')->count(),
            'cancelled' => Task::where('status', 'cancelled')->count(),
        ];

        $overdueTasks = Task::where('status', '!=', 'completed')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now()->toDateString())
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'projects' => [
                    'total' => $totalProjects,
                    'by_status' => $projectsByStatus,
                ],
                'staff' => [
                    'total' => $totalStaff,
                    'active' => $activeStaff,
                    'inactive' => $inactiveStaff,
                    'verified' => $verifiedStaff,
                ],
                'tasks' => [
                    'total' => $totalTasks,
                    'by_status' => $tasksByStatus,
                    'overdue' => $overdueTasks,
                    'completion_rate' => $totalTasks > 0 ? round(($tasksByStatus['completed'] / $totalTasks) * 100, 1) : 0,
                ],
            ],
        ]);
    }
}
