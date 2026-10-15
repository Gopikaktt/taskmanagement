<?php

namespace Tests\Feature;

use App\Jobs\SendTaskAssignedNotification;
use App\Models\Admin;
use App\Models\Project;
use App\Models\Staff;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TaskAssignmentAndNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_creating_task_with_staff_queues_notification_job(): void
    {
        Queue::fake();

        $admin = Admin::factory()->create();
        $staff = Staff::factory()->create([
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $project = Project::factory()->create(['created_by' => $admin->id]);

        $this->actingAs($admin, 'admin')->post('/admin/tasks', [
            'project_id' => $project->id,
            'staff_id' => $staff->id,
            'title' => 'Implement Automated Payment Webhooks',
            'description' => 'Handle Stripe and PayPal webhooks with signature checks',
            'priority' => 'high',
            'status' => 'pending',
            'due_date' => now()->addDays(7)->format('Y-m-d'),
        ]);

        Queue::assertPushed(SendTaskAssignedNotification::class, function ($job) use ($staff) {
            return $job->staff->id === $staff->id && $job->task->title === 'Implement Automated Payment Webhooks';
        });

        $this->assertDatabaseHas('tasks', [
            'title' => 'Implement Automated Payment Webhooks',
            'staff_id' => $staff->id,
        ]);
    }

    public function test_admin_api_task_creation_queues_notification_job(): void
    {
        Queue::fake();

        $admin = Admin::factory()->create();
        $token = $admin->createToken('admin-token', ['role:admin'])->plainTextToken;

        $staff = Staff::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $project = Project::factory()->create(['created_by' => $admin->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/admin/tasks', [
                'project_id' => $project->id,
                'staff_id' => $staff->id,
                'title' => 'API Task Creation Test',
                'description' => 'Testing API notification dispatch',
                'priority' => 'urgent',
                'status' => 'pending',
            ]);

        $response->assertStatus(201);

        Queue::assertPushed(SendTaskAssignedNotification::class);
    }

    public function test_staff_can_view_assigned_tasks_and_update_status(): void
    {
        $staff = Staff::factory()->create([
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $admin = Admin::factory()->create();
        $project = Project::factory()->create(['created_by' => $admin->id]);

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'staff_id' => $staff->id,
            'created_by' => $admin->id,
            'status' => 'in_progress',
        ]);

        $token = $staff->createToken('staff-token', ['role:staff'])->plainTextToken;

        // Staff updates status to completed
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->patchJson("/api/v1/staff/tasks/{$task->id}/status", [
                'status' => 'completed',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $task->id,
                    'status' => 'completed',
                ],
            ]);

        $task->refresh();
        $this->assertEquals('completed', $task->status);
        $this->assertNotNull($task->completed_at);
    }

    public function test_staff_cannot_update_tasks_assigned_to_another_staff_member(): void
    {
        $staff1 = Staff::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $staff2 = Staff::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin = Admin::factory()->create();
        $project = Project::factory()->create(['created_by' => $admin->id]);

        $taskOfStaff2 = Task::factory()->create([
            'project_id' => $project->id,
            'staff_id' => $staff2->id,
            'created_by' => $admin->id,
            'status' => 'pending',
        ]);

        $token1 = $staff1->createToken('staff-token', ['role:staff'])->plainTextToken;

        // Staff 1 tries to update task belonging to Staff 2
        $response = $this->withHeader('Authorization', 'Bearer ' . $token1)
            ->patchJson("/api/v1/staff/tasks/{$taskOfStaff2->id}/status", [
                'status' => 'completed',
            ]);

        $response->assertStatus(403);
    }
}
