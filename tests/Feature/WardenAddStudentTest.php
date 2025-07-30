<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Hostel;
use App\Models\RoomType;

class WardenAddStudentTest extends TestCase
{
    use RefreshDatabase;

    public function test_warden_can_add_student_with_usn(): void
    {
        // Create a warden
        $warden = User::factory()->create([
            'role' => 'warden',
            'usn' => 'WARDEN001'
        ]);

        // Create a hostel managed by the warden
        $hostel = Hostel::create([
            'name' => 'Test Hostel',
            'type' => 'boys',
            'address' => 'Test Address',
            'warden_id' => $warden->id,
            'status' => 'active'
        ]);

        // Create a room type
        $roomType = RoomType::create([
            'hostel_id' => $hostel->id,
            'type' => 'Single Room',
            'capacity' => 1,
            'price_per_month' => 5000,
            'total_rooms' => 10
        ]);

        $response = $this->actingAs($warden)->post("/warden/hostels/{$hostel->id}/students/add", [
            'name' => 'Test Student',
            'email' => 'student@test.com',
            'usn' => 'STU001',
            'phone' => '1234567890',
            'address' => 'Student Address',
            'hostel_id' => $hostel->id,
            'room_type_id' => $roomType->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify the student was created with USN
        $this->assertDatabaseHas('users', [
            'name' => 'Test Student',
            'email' => 'student@test.com',
            'usn' => 'STU001',
            'role' => 'student'
        ]);
    }

    public function test_warden_cannot_add_student_without_usn(): void
    {
        // Create a warden
        $warden = User::factory()->create([
            'role' => 'warden',
            'usn' => 'WARDEN001'
        ]);

        // Create a hostel managed by the warden
        $hostel = Hostel::create([
            'name' => 'Test Hostel',
            'type' => 'boys',
            'address' => 'Test Address',
            'warden_id' => $warden->id,
            'status' => 'active'
        ]);

        // Create a room type
        $roomType = RoomType::create([
            'hostel_id' => $hostel->id,
            'type' => 'Single Room',
            'capacity' => 1,
            'price_per_month' => 5000,
            'total_rooms' => 10
        ]);

        $response = $this->actingAs($warden)->post("/warden/hostels/{$hostel->id}/students/add", [
            'name' => 'Test Student',
            'email' => 'student@test.com',
            // Missing USN
            'phone' => '1234567890',
            'address' => 'Student Address',
            'hostel_id' => $hostel->id,
            'room_type_id' => $roomType->id,
        ]);

        $response->assertSessionHasErrors(['usn']);
    }

    public function test_warden_cannot_add_student_with_duplicate_usn(): void
    {
        // Create a warden
        $warden = User::factory()->create([
            'role' => 'warden',
            'usn' => 'WARDEN001'
        ]);

        // Create an existing student with USN
        User::create([
            'name' => 'Existing Student',
            'email' => 'existing@test.com',
            'usn' => 'STU001',
            'role' => 'student',
            'password' => bcrypt('password')
        ]);

        // Create a hostel managed by the warden
        $hostel = Hostel::create([
            'name' => 'Test Hostel',
            'type' => 'boys',
            'address' => 'Test Address',
            'warden_id' => $warden->id,
            'status' => 'active'
        ]);

        // Create a room type
        $roomType = RoomType::create([
            'hostel_id' => $hostel->id,
            'type' => 'Single Room',
            'capacity' => 1,
            'price_per_month' => 5000,
            'total_rooms' => 10
        ]);

        $response = $this->actingAs($warden)->post("/warden/hostels/{$hostel->id}/students/add", [
            'name' => 'Test Student',
            'email' => 'student@test.com',
            'usn' => 'STU001', // Duplicate USN
            'phone' => '1234567890',
            'address' => 'Student Address',
            'hostel_id' => $hostel->id,
            'room_type_id' => $roomType->id,
        ]);

        $response->assertSessionHasErrors(['usn']);
    }
} 