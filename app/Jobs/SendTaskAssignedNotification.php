<?php

namespace App\Jobs;

use App\Mail\TaskAssignedMail;
use App\Models\Staff;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTaskAssignedNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Task $task;
    public Staff $staff;

    /**
     * Create a new job instance.
     */
    public function __construct(Task $task, Staff $staff)
    {
        $this->task = $task;
        $this->staff = $staff;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Dispatching task assignment email notification for Task #{$this->task->id} ({$this->task->title}) to Staff: {$this->staff->email}");

        try {
            Mail::to($this->staff->email)->send(new TaskAssignedMail($this->task, $this->staff));
            Log::info("Successfully sent task assignment email to {$this->staff->email}");
        } catch (\Throwable $e) {
            Log::error("Failed to send task assignment email to {$this->staff->email}: " . $e->getMessage(), [
                'task_id' => $this->task->id,
                'staff_id' => $this->staff->id,
                'exception' => $e,
            ]);
        }
    }
}
