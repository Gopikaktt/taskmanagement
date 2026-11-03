<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the Staff Dashboard (Supports both API JSON and Inertia Web).
     */
    public function index(Request $request): JsonResponse|Response
    {
        $staff = $request->user() ?? Auth::guard('staff')->user();

        $stats = [
            'total_assigned' => Task::where('staff_id', $staff->id)->count(),
            'pending' => Task::where('staff_id', $staff->id)->where('status', 'pending')->count(),
            'in_progress' => Task::where('staff_id', $staff->id)->where('status', 'in_progress')->count(),
            'completed' => Task::where('staff_id', $staff->id)->where('status', 'completed')->count(),
            'overdue' => Task::where('staff_id', $staff->id)
                ->where('status', '!=', 'completed')
                ->whereNotNull('due_date')
                ->where('due_date', '<', now()->toDateString())
                ->count(),
        ];

        $recentTasks = Task::where('staff_id', $staff->id)
            ->with(['project', 'creator'])
            ->latest()
            ->take(6)
            ->get();

        if ($request->is('api/*') || ($request->wantsJson() && !$request->header('X-Inertia'))) {
            return response()->json([
                'success' => true,
                'data' => [
                    'staff' => $staff,
                    'stats' => $stats,
                    'recent_tasks' => $recentTasks,
                ],
            ]);
        }

        return Inertia::render('Staff/Dashboard', [
            'staff' => $staff,
            'stats' => $stats,
            'recentTasks' => $recentTasks,
        ]);
    }
}

