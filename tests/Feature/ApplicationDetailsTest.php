<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Hostel;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\RoomApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApplicationDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_application_details_page_loads()
    {
        // Create a warden user
        $warden = User::factory()->create([
            'role' => 'warden'
        ]);

        // Create a student user
        $student = User::factory()->create([
            'role' => 'student'
        ]);

        // Create a hostel
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
            'type' => 'Single',
            'capacity' => 1,
            'price_per_month' => 5000,
            'total_rooms' => 1
        ]);

        // Create a room
        $room = Room::create([
            'hostel_id' => $hostel->id,
            'room_type_id' => $roomType->id,
            'room_number' => '101',
            'floor' => '1',
            'status' => 'available',
            'current_occupants' => 0,
            'max_occupants' => 1
        ]);

        // Create a room application
        $application = RoomApplication::create([
            'student_id' => $student->id,
            'hostel_id' => $hostel->id,
            'room_type_id' => $roomType->id,
            'application_date' => now(),
            'status' => 'pending'
        ]);

        // Act as the warden and visit the application details page
        $response = $this->actingAs($warden)
            ->get('/warden/applications/' . $application->id);

        // Assert the page loads successfully
        $response->assertStatus(200);
        $response->assertSee('Application Details');
        $response->assertSee($student->name);
        $response->assertSee($hostel->name);
        $response->assertSee($roomType->type);
        $response->assertSee('Process Application');
    }

    public function test_application_details_page_shows_approved_application()
    {
        // Create a warden user
        $warden = User::factory()->create([
            'role' => 'warden'
        ]);

        // Create a student user
        $student = User::factory()->create([
            'role' => 'student'
        ]);

        // Create a hostel
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
            'type' => 'Single',
            'capacity' => 1,
            'price_per_month' => 5000,
            'total_rooms' => 1
        ]);

        // Create an approved room application
        $application = RoomApplication::create([
            'student_id' => $student->id,
            'hostel_id' => $hostel->id,
            'room_type_id' => $roomType->id,
            'application_date' => now(),
            'status' => 'approved',
            'warden_remarks' => 'Approved for room assignment',
            'processed_by' => $warden->id,
            'processed_at' => now()
        ]);

        // Act as the warden and visit the application details page
        $response = $this->actingAs($warden)
            ->get('/warden/applications/' . $application->id);

        // Assert the page loads successfully
        $response->assertStatus(200);
        $response->assertSee('Application Details');
        $response->assertSee($student->name);
        $response->assertSee('Approved');
        $response->assertDontSee('Process Application'); // Should not show process form for approved apps
    }
} 