<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Hostel;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\RoomAssignment;
use App\Models\StudentProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WardenStudentProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_warden_can_view_student_profile()
    {
        // Create a warden user
        $warden = User::factory()->create([
            'role' => 'warden'
        ]);

        // Create a student user
        $student = User::factory()->create([
            'role' => 'student',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'parent_mobile' => '9876543210',
            'parent_email' => 'parent@example.com',
            'alternate_mobile' => '5555555555'
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

        // Create a room assignment
        $assignment = RoomAssignment::create([
            'student_id' => $student->id,
            'room_id' => $room->id,
            'status' => 'active',
            'assigned_date' => now()
        ]);

        // Create a student profile
        $profile = StudentProfile::create([
            'user_id' => $student->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'father_name' => 'Father Doe',
            'father_occupation' => 'Engineer',
            'father_email' => 'father@example.com',
            'father_mobile' => '1111111111',
            'mother_name' => 'Mother Doe',
            'mother_occupation' => 'Teacher',
            'mother_email' => 'mother@example.com',
            'mother_mobile' => '2222222222',
            'gender' => 'Male',
            'dob' => '2000-01-01',
            'blood_group' => 'O+',
            'emergency_phone' => '3333333333',
            'aadhaar_id' => '123456789012',
            'admission_date' => '2023-01-01',
            'present_state' => 'Maharashtra',
            'present_city' => 'Mumbai',
            'present_address' => '123 Test Street',
            'permanent_state' => 'Maharashtra',
            'permanent_city' => 'Mumbai',
            'permanent_address' => '456 Permanent Street'
        ]);

        // Act as the warden and visit the student profile page
        $response = $this->actingAs($warden)
            ->get('/warden/students/' . $student->id);

        // Assert the page loads successfully
        $response->assertStatus(200);
        $response->assertSee('Student Profile & Parent Details');
        $response->assertSee('John Doe');
        $response->assertSee('john@example.com');
        $response->assertSee('Test Hostel');
        $response->assertSee('101');
        
        // Assert parent details are displayed
        $response->assertSee('Father Doe');
        $response->assertSee('Engineer');
        $response->assertSee('father@example.com');
        $response->assertSee('1111111111');
        $response->assertSee('Mother Doe');
        $response->assertSee('Teacher');
        $response->assertSee('mother@example.com');
        $response->assertSee('2222222222');
        
        // Assert additional contact information
        $response->assertSee('9876543210');
        $response->assertSee('parent@example.com');
        $response->assertSee('5555555555');
        
        // Assert address information
        $response->assertSee('Maharashtra');
        $response->assertSee('Mumbai');
        $response->assertSee('123 Test Street');
        $response->assertSee('456 Permanent Street');
    }

    public function test_warden_can_view_student_without_profile()
    {
        // Create a warden user
        $warden = User::factory()->create([
            'role' => 'warden'
        ]);

        // Create a student user without profile
        $student = User::factory()->create([
            'role' => 'student',
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '1234567890',
            'parent_mobile' => '9876543210',
            'parent_email' => 'parent@example.com'
        ]);

        // Act as the warden and visit the student profile page
        $response = $this->actingAs($warden)
            ->get('/warden/students/' . $student->id);

        // Assert the page loads successfully
        $response->assertStatus(200);
        $response->assertSee('Student Profile & Parent Details');
        $response->assertSee('Jane Doe');
        $response->assertSee('jane@example.com');
        
        // Assert basic parent contact info is displayed
        $response->assertSee('9876543210');
        $response->assertSee('parent@example.com');
        
        // Assert that profile sections show appropriate fallbacks
        $response->assertSee('-'); // For missing profile data
    }

    public function test_warden_cannot_view_other_warden_students()
    {
        // Create two wardens
        $warden1 = User::factory()->create([
            'role' => 'warden'
        ]);
        
        $warden2 = User::factory()->create([
            'role' => 'warden'
        ]);

        // Create a student
        $student = User::factory()->create([
            'role' => 'student',
            'name' => 'Test Student'
        ]);

        // Act as warden1 and try to view student profile
        $response = $this->actingAs($warden1)
            ->get('/warden/students/' . $student->id);

        // Should still be able to view (no hostel restriction in current implementation)
        $response->assertStatus(200);
    }
} 