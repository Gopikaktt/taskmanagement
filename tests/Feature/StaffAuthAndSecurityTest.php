<?php

namespace Tests\Feature;

use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StaffAuthAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_verified_staff_can_login_via_web(): void
    {
        $staff = Staff::factory()->create([
            'email' => 'active.staff@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/staff/login', [
            'email' => 'active.staff@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/staff/dashboard');
        $this->assertAuthenticatedAs($staff, 'staff');
    }

    public function test_staff_login_with_remember_me_saves_cookie_and_prefills_email(): void
    {
        $staff = Staff::factory()->create([
            'email' => 'staff.remember@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/staff/login', [
            'email' => 'staff.remember@example.com',
            'password' => 'password123',
            'remember' => '1',
        ]);

        $response->assertRedirect('/staff/dashboard');
        $response->assertCookie('remembered_staff_email', 'staff.remember@example.com', false);
        $response->assertCookie('remembered_staff_password', base64_encode('password123'), false);

        // Logout
        $this->post('/staff/logout');

        // Visit staff login page with cookies
        $loginResponse = $this->withUnencryptedCookie('remembered_staff_email', 'staff.remember@example.com')
            ->withUnencryptedCookie('remembered_staff_password', base64_encode('password123'))
            ->get('/staff/login');

        $loginResponse->assertStatus(200);
        $loginResponse->assertSee('value="staff.remember@example.com"', false);
        $loginResponse->assertSee('value="password123"', false);
        $loginResponse->assertSee('checked', false);
    }

    public function test_inactive_staff_is_blocked_from_web_login(): void
    {
        Staff::factory()->inactive()->create([
            'email' => 'inactive@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/staff/login', [
            'email' => 'inactive@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('staff');
    }

    public function test_unverified_staff_is_blocked_from_web_login(): void
    {
        Staff::factory()->unverified()->create([
            'email' => 'unverified@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $response = $this->post('/staff/login', [
            'email' => 'unverified@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('staff');
    }

    public function test_active_verified_staff_can_login_via_api(): void
    {
        Staff::factory()->create([
            'email' => 'staff.api@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/staff/login', [
            'email' => 'staff.api@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token',
                    'token_type',
                    'staff' => ['id', 'name', 'email', 'is_active', 'is_verified'],
                ],
            ]);
    }

    public function test_inactive_staff_api_login_returns_403_forbidden(): void
    {
        Staff::factory()->inactive()->create([
            'email' => 'inactive.api@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/staff/login', [
            'email' => 'inactive.api@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'account_status' => 'inactive',
            ]);
    }

    public function test_unverified_staff_api_login_returns_403_forbidden(): void
    {
        Staff::factory()->unverified()->create([
            'email' => 'unverified.api@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/v1/staff/login', [
            'email' => 'unverified.api@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'account_status' => 'unverified',
            ]);
    }

    public function test_deactivated_staff_token_is_blocked_by_middleware(): void
    {
        $staff = Staff::factory()->create([
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $token = $staff->createToken('staff-token', ['role:staff'])->plainTextToken;

        // Deactivate staff in database
        $staff->is_active = false;
        $staff->save();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/staff/tasks');

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'account_status' => 'inactive',
            ]);
    }
}
