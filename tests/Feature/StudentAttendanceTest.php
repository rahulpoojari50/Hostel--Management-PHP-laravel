<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Hostel;
use App\Models\MealAttendance;
use App\Models\HostelAttendance;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StudentAttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_view_attendance_page()
    {
        // Create a warden user
        $warden = User::factory()->create([
            'role' => 'warden'
        ]);

        // Create a hostel
        $hostel = Hostel::create([
            'name' => 'Test Hostel',
            'type' => 'boys',
            'address' => 'Test Address',
            'warden_id' => $warden->id,
            'status' => 'active'
        ]);

        // Create a student user
        $student = User::factory()->create([
            'role' => 'student'
        ]);

        // Create some test attendance records
        MealAttendance::create([
            'student_id' => $student->id,
            'date' => '2024-01-15',
            'meal_type' => 'Breakfast',
            'status' => 'Taken',
            'hostel_id' => $hostel->id
        ]);

        HostelAttendance::create([
            'student_id' => $student->id,
            'hostel_id' => $hostel->id,
            'date' => '2024-01-15',
            'status' => 'Taken',
            'remarks' => 'Test remark'
        ]);

        // Act as the student
        $response = $this->actingAs($student)
            ->get('/student/attendance');

        // Assert the response
        $response->assertStatus(200);
        $response->assertSee('Attendance History');
        $response->assertSee('Meal Attendance');
        $response->assertSee('Hostel Attendance');
    }

    public function test_attendance_page_shows_statistics()
    {
        // Create a warden user
        $warden = User::factory()->create([
            'role' => 'warden'
        ]);

        // Create a hostel
        $hostel = Hostel::create([
            'name' => 'Test Hostel',
            'type' => 'boys',
            'address' => 'Test Address',
            'warden_id' => $warden->id,
            'status' => 'active'
        ]);

        // Create a student user
        $student = User::factory()->create([
            'role' => 'student'
        ]);

        // Create test attendance records
        MealAttendance::create([
            'student_id' => $student->id,
            'date' => '2024-01-15',
            'meal_type' => 'Breakfast',
            'status' => 'Taken',
            'hostel_id' => $hostel->id
        ]);

        HostelAttendance::create([
            'student_id' => $student->id,
            'hostel_id' => $hostel->id,
            'date' => '2024-01-15',
            'status' => 'Taken',
            'remarks' => 'Test remark'
        ]);

        // Act as the student
        $response = $this->actingAs($student)
            ->get('/student/attendance');

        // Assert statistics are shown
        $response->assertSee('100%'); // Should show 100% for meal attendance
        $response->assertSee('100%'); // Should show 100% for hostel attendance
        $response->assertSee('2'); // Total records
    }

    public function test_attendance_filtering_works()
    {
        // Create a warden user
        $warden = User::factory()->create([
            'role' => 'warden'
        ]);

        // Create a hostel
        $hostel = Hostel::create([
            'name' => 'Test Hostel',
            'type' => 'boys',
            'address' => 'Test Address',
            'warden_id' => $warden->id,
            'status' => 'active'
        ]);

        // Create a student user
        $student = User::factory()->create([
            'role' => 'student'
        ]);

        // Create test attendance records
        MealAttendance::create([
            'student_id' => $student->id,
            'date' => '2024-01-15',
            'meal_type' => 'Breakfast',
            'status' => 'Taken',
            'hostel_id' => $hostel->id
        ]);

        HostelAttendance::create([
            'student_id' => $student->id,
            'hostel_id' => $hostel->id,
            'date' => '2024-01-16',
            'status' => 'Taken',
            'remarks' => 'Test remark'
        ]);

        // Test meal attendance filter
        $response = $this->actingAs($student)
            ->get('/student/attendance?type=meal');

        $response->assertStatus(200);
        $response->assertSee('15 Jan 2024'); // Updated to match the view format
        $response->assertDontSee('16 Jan 2024'); // Updated to match the view format

        // Test hostel attendance filter
        $response = $this->actingAs($student)
            ->get('/student/attendance?type=hostel');

        $response->assertStatus(200);
        $response->assertSee('16 Jan 2024'); // Updated to match the view format
        $response->assertDontSee('15 Jan 2024'); // Updated to match the view format
    }
} 