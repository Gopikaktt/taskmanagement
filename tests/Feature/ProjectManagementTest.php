<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Project;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_project_via_web(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->post('/admin/projects', [
            'name' => 'Global Payment Gateway Redesign',
            'description' => 'Comprehensive overhaul of international checkout flows',
            'status' => 'in_progress',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(30)->format('Y-m-d'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('projects', [
            'name' => 'Global Payment Gateway Redesign',
            'created_by' => $admin->id,
        ]);
    }

    public function test_staff_cannot_create_or_delete_projects(): void
    {
        $staff = Staff::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $project = Project::factory()->create();

        // Staff tries to access admin project route
        $response = $this->actingAs($staff, 'staff')->delete("/admin/projects/{$project->id}");

        // Should be redirected to admin login because staff guard is not admin
        $response->assertRedirect('/admin/login');
        $this->assertDatabaseHas('projects', ['id' => $project->id]);
    }
}
