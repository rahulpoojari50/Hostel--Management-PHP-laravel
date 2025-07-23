<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed_for_student(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $response = $this
            ->actingAs($user)
            ->get('/student/profile');

        $response->assertOk();
    }

    public function test_profile_page_is_displayed_for_warden(): void
    {
        $user = User::factory()->create(['role' => 'warden']);

        $response = $this
            ->actingAs($user)
            ->get('/warden/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $response = $this
            ->actingAs($user)
            ->patch('/student/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/student/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $response = $this
            ->actingAs($user)
            ->patch('/student/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/student/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $response = $this
            ->actingAs($user)
            ->delete('/student/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $response = $this
            ->actingAs($user)
            ->from('/student/profile')
            ->delete('/student/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/student/profile');

        $this->assertNotNull($user->fresh());
    }
}
