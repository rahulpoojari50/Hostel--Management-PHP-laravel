<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class USNLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_login_with_email()
    {
        // Create a student user
        $student = User::factory()->create([
            'role' => 'student',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'usn' => '1MS21CS001',
            'password' => bcrypt('password123')
        ]);

        // Attempt to login with email
        $response = $this->post('/login', [
            'email' => 'john@example.com',
            'password' => 'password123',
            'role' => 'student'
        ]);

        // Assert successful login
        $response->assertRedirect();
        $this->assertAuthenticated();
        $this->assertAuthenticatedAs($student);
    }

    public function test_student_can_login_with_usn()
    {
        // Create a student user
        $student = User::factory()->create([
            'role' => 'student',
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'usn' => '1MS21CS002',
            'password' => bcrypt('password123')
        ]);

        // Attempt to login with USN
        $response = $this->post('/login', [
            'email' => '1MS21CS002',
            'password' => 'password123',
            'role' => 'student'
        ]);

        // Debug: Check if user exists
        $foundUser = User::where('usn', '1MS21CS002')->first();
        if (!$foundUser) {
            $this->fail('User with USN 1MS21CS002 not found in database');
        }

        // Assert successful login
        $response->assertRedirect();
        $this->assertAuthenticated();
        $this->assertAuthenticatedAs($student);
    }

    public function test_warden_can_login_with_email()
    {
        // Create a warden user
        $warden = User::factory()->create([
            'role' => 'warden',
            'name' => 'Admin Warden',
            'email' => 'warden@example.com',
            'usn' => 'WARDEN001',
            'password' => bcrypt('password123')
        ]);

        // Attempt to login with email
        $response = $this->post('/login', [
            'email' => 'warden@example.com',
            'password' => 'password123',
            'role' => 'warden'
        ]);

        // Assert successful login
        $response->assertRedirect();
        $this->assertAuthenticated();
        $this->assertAuthenticatedAs($warden);
    }

    public function test_warden_can_login_with_usn()
    {
        // Create a warden user
        $warden = User::factory()->create([
            'role' => 'warden',
            'name' => 'Admin Warden',
            'email' => 'warden@example.com',
            'usn' => 'WARDEN001',
            'password' => bcrypt('password123')
        ]);

        // Attempt to login with USN
        $response = $this->post('/login', [
            'email' => 'WARDEN001',
            'password' => 'password123',
            'role' => 'warden'
        ]);

        // Assert successful login
        $response->assertRedirect();
        $this->assertAuthenticated();
        $this->assertAuthenticatedAs($warden);
    }

    public function test_login_fails_with_invalid_credentials()
    {
        // Create a student user
        $student = User::factory()->create([
            'role' => 'student',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'usn' => '1MS21CS001',
            'password' => bcrypt('password123')
        ]);

        // Attempt to login with invalid USN
        $response = $this->post('/login', [
            'email' => 'INVALID123',
            'password' => 'password123',
            'role' => 'student'
        ]);

        // Assert login fails
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_registration_requires_usn()
    {
        // Attempt to register without USN
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student'
        ]);

        // Assert validation error for USN
        $response->assertSessionHasErrors(['usn']);
    }

    public function test_registration_with_usn_succeeds()
    {
        // Attempt to register with USN
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'usn' => '1MS21CS001',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student'
        ]);

        // Assert successful registration
        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'usn' => '1MS21CS001',
            'role' => 'student'
        ]);
    }

    public function test_usn_must_be_unique()
    {
        // Create first user
        User::factory()->create([
            'role' => 'student',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'usn' => '1MS21CS001',
            'password' => bcrypt('password123')
        ]);

        // Attempt to register with same USN
        $response = $this->post('/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'usn' => '1MS21CS001',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student'
        ]);

        // Assert validation error for duplicate USN
        $response->assertSessionHasErrors(['usn']);
    }
} 