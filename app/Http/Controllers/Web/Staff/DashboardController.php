<?php

namespace App\Http\Controllers\Web\Staff;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Staff Dashboard.
     */
    public function index(): View
    {
        $staff = Auth::guard('staff')->user();

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

        return view('staff.dashboard', compact('staff', 'stats', 'recentTasks'));
    }
}
