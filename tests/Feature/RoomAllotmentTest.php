<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Hostel;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\RoomApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoomAllotmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_approve_button_redirects_to_room_allotment()
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

        // Act as the warden and visit the applications page
        $response = $this->actingAs($warden)
            ->get('/warden/applications');

        // Assert the page loads successfully
        $response->assertStatus(200);
        $response->assertSee($student->name);

        // Test that the approve button redirects to room allotment
        $response = $this->actingAs($warden)
            ->get('/warden/room-allotment/' . $application->id);

        // Assert the room allotment page loads
        $response->assertStatus(200);
        $response->assertSee('Allot Room to ' . $student->name);
        $response->assertSee('Select Available Room');
    }

    public function test_room_allotment_process()
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

        // Act as the warden and submit room allotment
        $response = $this->actingAs($warden)
            ->post('/warden/room-allotment/' . $application->id . '/allot', [
                'room_id' => $room->id,
                'warden_remarks' => 'Test allotment'
            ]);

        // Assert successful redirect
        $response->assertRedirect('/warden/room-allotment');
        $response->assertSessionHas('success');

        // Assert application is approved
        $application->refresh();
        $this->assertEquals('approved', $application->status);
        $this->assertEquals($warden->id, $application->processed_by);

        // Assert room assignment is created
        $this->assertDatabaseHas('room_assignments', [
            'student_id' => $student->id,
            'room_id' => $room->id,
            'status' => 'active'
        ]);

        // Assert room occupancy is updated
        $room->refresh();
        $this->assertEquals(1, $room->current_occupants);
        $this->assertEquals('occupied', $room->status);
    }
} 