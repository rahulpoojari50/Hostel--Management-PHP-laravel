<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'usn' => 'STU001',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'student',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('student.dashboard', absolute: false));
    }

    public function test_new_wardens_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Warden',
            'email' => 'warden@example.com',
            'usn' => 'WARDEN001',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'warden',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('warden.dashboard', absolute: false));
    }
}
