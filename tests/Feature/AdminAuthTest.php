<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_login_page(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertSee('Admin Portal');
    }

    public function test_admin_can_authenticate_via_web_session(): void
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function test_admin_login_with_remember_me_saves_cookie_and_prefills_email(): void
    {
        $admin = Admin::factory()->create([
            'email' => 'admin.remember@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin.remember@example.com',
            'password' => 'password123',
            'remember' => '1',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $response->assertCookie('remembered_admin_email', 'admin.remember@example.com', false);
        $response->assertCookie('remembered_admin_password', base64_encode('password123'), false);

        // Logout
        $this->post('/admin/logout');

        // Visit login page with cookies
        $loginResponse = $this->withUnencryptedCookie('remembered_admin_email', 'admin.remember@example.com')
            ->withUnencryptedCookie('remembered_admin_password', base64_encode('password123'))
            ->get('/admin/login');

        $loginResponse->assertStatus(200);
        $loginResponse->assertSee('value="admin.remember@example.com"', false);
        $loginResponse->assertSee('value="password123"', false);
        $loginResponse->assertSee('checked', false);
    }

    public function test_admin_cannot_login_with_invalid_credentials(): void
    {
        Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }

    public function test_admin_can_authenticate_via_api_and_receive_sanctum_token(): void
    {
        Admin::factory()->create([
            'email' => 'admin.api@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/admin/login', [
            'email' => 'admin.api@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token',
                    'token_type',
                    'admin' => ['id', 'name', 'email'],
                ],
            ]);
    }

    public function test_admin_api_token_can_access_admin_endpoints(): void
    {
        $admin = Admin::factory()->create();
        $token = $admin->createToken('admin-token', ['role:admin'])->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/admin/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['projects', 'staff', 'tasks'],
            ]);
    }
}
