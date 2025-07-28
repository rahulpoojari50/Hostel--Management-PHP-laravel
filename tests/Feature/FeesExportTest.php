<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Hostel;
use App\Models\StudentFee;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FeesExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_warden_can_export_fees_csv()
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
            'usn' => '1MS21CS001',
            'parent_email' => 'parent@example.com'
        ]);

        // Create a hostel
        $hostel = Hostel::create([
            'name' => 'Test Hostel',
            'type' => 'boys',
            'address' => 'Test Address',
            'warden_id' => $warden->id,
            'status' => 'active'
        ]);

        // Create a student fee
        $fee = StudentFee::create([
            'student_id' => $student->id,
            'hostel_id' => $hostel->id,
            'fee_type' => 'hostel_fee',
            'amount' => 5000.00,
            'status' => 'pending',
            'due_date' => now()->addMonth()
        ]);

        // Act as the warden and export CSV
        $response = $this->actingAs($warden)
            ->get('/warden/fees/student-status/export/csv');

        // Debug: Check if we have students in the database
        $studentCount = User::where('role', 'student')->count();
        $this->assertGreaterThan(0, $studentCount, "No students found in database");

        // Assert the response is successful
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="student_fees_status_' . date('Y-m-d_H-i-s') . '.csv"');
        
        // Assert CSV content contains expected data
        $content = $response->getContent();
        // Debug: Let's see what we actually get
        if (empty($content)) {
            $this->fail('CSV content is empty. Response: ' . $response->getContent());
        }
        $this->assertStringContainsString('Student Name', $content);
        $this->assertStringContainsString('USN', $content);
        $this->assertStringContainsString('John Doe', $content);
        $this->assertStringContainsString('1MS21CS001', $content);
        $this->assertStringContainsString('john@example.com', $content);
        $this->assertStringContainsString('parent@example.com', $content);
        $this->assertStringContainsString('Pending', $content);
        $this->assertStringContainsString('5,000.00', $content);
    }

    public function test_warden_can_export_fees_pdf()
    {
        // Create a warden user
        $warden = User::factory()->create([
            'role' => 'warden'
        ]);

        // Create a student user
        $student = User::factory()->create([
            'role' => 'student',
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'usn' => '1MS21CS002',
            'parent_email' => 'parent@example.com'
        ]);

        // Create a hostel
        $hostel = Hostel::create([
            'name' => 'Test Hostel',
            'type' => 'boys',
            'address' => 'Test Address',
            'warden_id' => $warden->id,
            'status' => 'active'
        ]);

        // Create a student fee
        $fee = StudentFee::create([
            'student_id' => $student->id,
            'hostel_id' => $hostel->id,
            'fee_type' => 'mess_fee',
            'amount' => 3000.00,
            'status' => 'paid',
            'due_date' => now()->addMonth()
        ]);

        // Act as the warden and export PDF
        $response = $this->actingAs($warden)
            ->get('/warden/fees/student-status/export/pdf');

        // Assert the response is successful
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertHeader('Content-Disposition', 'attachment; filename=student_fees_status_' . date('Y-m-d_H-i-s') . '.pdf');
    }

    public function test_warden_can_export_fees_word()
    {
        // Create a warden user
        $warden = User::factory()->create([
            'role' => 'warden'
        ]);

        // Create a student user
        $student = User::factory()->create([
            'role' => 'student',
            'name' => 'Bob Smith',
            'email' => 'bob@example.com',
            'usn' => '1MS21CS003',
            'parent_email' => 'parent@example.com'
        ]);

        // Create a hostel
        $hostel = Hostel::create([
            'name' => 'Test Hostel',
            'type' => 'boys',
            'address' => 'Test Address',
            'warden_id' => $warden->id,
            'status' => 'active'
        ]);

        // Create a student fee
        $fee = StudentFee::create([
            'student_id' => $student->id,
            'hostel_id' => $hostel->id,
            'fee_type' => 'security_deposit',
            'amount' => 10000.00,
            'status' => 'pending',
            'due_date' => now()->addMonth()
        ]);

        // Act as the warden and export Word
        $response = $this->actingAs($warden)
            ->get('/warden/fees/student-status/export/word');

        // Assert the response is successful
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $response->assertHeader('Content-Disposition', 'attachment; filename=student_fees_status_' . date('Y-m-d_H-i-s') . '.docx');
    }

    public function test_export_respects_search_filter()
    {
        // Create a warden user
        $warden = User::factory()->create([
            'role' => 'warden'
        ]);

        // Create students
        $student1 = User::factory()->create([
            'role' => 'student',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'usn' => '1MS21CS001'
        ]);

        $student2 = User::factory()->create([
            'role' => 'student',
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'usn' => '1MS21CS002'
        ]);

        // Create a hostel
        $hostel = Hostel::create([
            'name' => 'Test Hostel',
            'type' => 'boys',
            'address' => 'Test Address',
            'warden_id' => $warden->id,
            'status' => 'active'
        ]);

        // Create fees
        StudentFee::create([
            'student_id' => $student1->id,
            'hostel_id' => $hostel->id,
            'fee_type' => 'hostel_fee',
            'amount' => 5000.00,
            'status' => 'pending',
            'due_date' => now()->addMonth()
        ]);

        StudentFee::create([
            'student_id' => $student2->id,
            'hostel_id' => $hostel->id,
            'fee_type' => 'mess_fee',
            'amount' => 3000.00,
            'status' => 'paid',
            'due_date' => now()->addMonth()
        ]);

        // Act as the warden and export CSV with search filter
        $response = $this->actingAs($warden)
            ->get('/warden/fees/student-status/export/csv?search=John');

        // Assert the response is successful
        $response->assertStatus(200);
        
        // Assert CSV content contains only John's data
        $content = $response->getContent();
        $this->assertStringContainsString('John Doe', $content);
        $this->assertStringNotContainsString('Jane Smith', $content);
    }

    public function test_export_handles_no_data()
    {
        // Create a warden user
        $warden = User::factory()->create([
            'role' => 'warden'
        ]);

        // Act as the warden and export CSV with no students
        $response = $this->actingAs($warden)
            ->get('/warden/fees/student-status/export/csv');

        // Assert the response is successful
        $response->assertStatus(200);
        
        // Assert CSV content contains headers but no data
        $content = $response->getContent();
        $this->assertStringContainsString('Student Name', $content);
        $this->assertStringContainsString('Email', $content);
        $this->assertStringContainsString('Parent Email', $content);
        $this->assertStringContainsString('Hostel Name', $content);
    }
} 