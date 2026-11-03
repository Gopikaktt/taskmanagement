<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Staff;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAndPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed roles and permissions for tests
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_manager_staff_can_access_manager_only_test_route(): void
    {
        $manager = Staff::factory()->create([
            'email' => 'staff@example.com',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Re-seed or assign manager role
        $this->seed(RoleAndPermissionSeeder::class);

        $response = $this->actingAs($manager, 'staff')
            ->get('/staff/manager-only-test');

        $response->assertStatus(200);
        $response->assertSee('Access Granted: You are verified as a Manager!');
    }

    public function test_regular_staff_without_manager_role_is_forbidden(): void
    {
        $regularStaff = Staff::factory()->create([
            'email' => 'sarah.smith@example.com',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->seed(RoleAndPermissionSeeder::class);

        $response = $this->actingAs($regularStaff, 'staff')
            ->get('/staff/manager-only-test');

        $response->assertStatus(403);
    }

    public function test_admin_has_super_admin_role_and_permissions(): void
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
        ]);

        $this->seed(RoleAndPermissionSeeder::class);

        $admin->refresh();
        $this->assertTrue($admin->hasRole('super-admin', 'admin'));
        $this->assertTrue($admin->can('manage projects'));
        $this->assertTrue($admin->can('manage staff'));
    }
}
