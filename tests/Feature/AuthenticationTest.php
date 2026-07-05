<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $this->assertGuest();

        $this->actingAs($user);

        $this->assertAuthenticatedAs($user);
    }

    public function test_non_admin_cannot_access_filament_panel(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertForbidden();
    }

    public function test_admin_can_access_filament_panel(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
    }

    public function test_guest_is_redirected_from_admin_panel(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect();
    }
}
