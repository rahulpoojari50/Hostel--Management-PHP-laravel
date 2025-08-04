<?php

namespace App\Http\Controllers\Warden;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hostel;
use App\Models\StudentFee;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\PendingFeesNotification;
use App\Notifications\PendingFeeNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

class FeesController extends Controller
{
    public function index(Request $request)
    {
        // For now, just show the Add Fees form for the first hostel managed by the warden
        $hostel = Hostel::where('warden_id', auth()->id())->first();
        
        $pageTitle = 'Add Fees';
        $breadcrumbs = [
            ['name' => 'Home', 'url' => url('/warden/dashboard')],
            ['name' => 'Fees', 'url' => route('warden.fees.index')],
            ['name' => 'Add Fees', 'url' => '']
        ];
        
        return view('warden.fees.index', compact('hostel', 'pageTitle', 'breadcrumbs'));
    }

    public function createMissingFees($hostelId)
    {
        $hostel = Hostel::where('warden_id', auth()->id())->findOrFail($hostelId);
        
        if (!$hostel->fees || !is_array($hostel->fees)) {
            return redirect()->back()->with('error', 'No fees defined for this hostel.');
        }
        
        // Get all students in this hostel
        $students = User::whereHas('roomAssignments.room', function($q) use ($hostel) {
            $q->where('hostel_id', $hostel->id);
        })->get();
        
        $createdFees = 0;
        $existingFees = 0;
        
        foreach ($students as $student) {
            foreach ($hostel->fees as $fee) {
                // Check if this fee type already exists for this student
                $existingFee = StudentFee::where('student_id', $student->id)
                    ->where('hostel_id', $hostel->id)
                    ->where('fee_type', $fee['type'])
                    ->first();
                
                // Only create if it doesn't exist
                if (!$existingFee) {
                    StudentFee::create([
                        'student_id' => $student->id,
                        'hostel_id' => $hostel->id,
                        'fee_type' => $fee['type'],
                        'amount' => $fee['amount'],
                        'status' => 'pending',
                    ]);
                    $createdFees++;
                } else {
                    $existingFees++;
                }
            }
        }
        
        $message = "Fee creation completed. ";
        if ($createdFees > 0) {
            $message .= "Created {$createdFees} new pending fee(s) for {$students->count()} student(s). ";
        }
        if ($existingFees > 0) {
            $message .= "{$existingFees} fee(s) already existed. ";
        }
        if ($createdFees === 0 && $existingFees === 0) {
            $message .= "No new fees were created (all fees already exist).";
        }
        
        return redirect()->back()->with('success', $message);
    }

    public function studentStatus(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $hostels = Hostel::where('warden_id', auth()->id())->get();
        $selectedHostelId = $request->input('hostel_id');
        $selectedHostel = $hostels->where('id', $selectedHostelId)->first();
        
        $query = User::where('role', 'student')->with(['studentFees', 'roomAssignments.room.hostel', 'studentProfile']);
        
        // Filter by hostel if selected
        if ($selectedHostel) {
            $query->whereHas('roomAssignments.room', function($q) use ($selectedHostel) {
                $q->where('hostel_id', $selectedHostel->id);
            });
        }
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('usn', 'like', "%$search%")
                  ->orWhere('parent_email', 'like', "%$search%")
                  ;
            });
        }
        
        $students = $query->paginate($perPage)->appends($request->only('search', 'per_page', 'hostel_id'));
        
        // Get fee types based on selected hostel or all fee types if no hostel selected
        if ($selectedHostel && is_array($selectedHostel->fees)) {
            $feeTypes = collect($selectedHostel->fees)->pluck('type')->toArray();
        } else {
            $feeTypes = \App\Models\StudentFee::distinct()->pluck('fee_type')->toArray();
        }
        
        $pageTitle = 'Student Fee Status';
        $breadcrumbs = [
            ['name' => 'Home', 'url' => url('/warden/dashboard')],
            ['name' => 'Fees', 'url' => route('warden.fees.index')],
            ['name' => 'Student Status', 'url' => '']
        ];
        
        return view('warden.fees.student_status', compact('students', 'feeTypes', 'hostels', 'selectedHostel', 'selectedHostelId', 'pageTitle', 'breadcrumbs'));
    }

    public function exportCsv(Request $request)
    {
        $hostels = Hostel::where('warden_id', auth()->id())->get();
        $selectedHostelId = $request->input('hostel_id');
        $selectedHostel = $hostels->where('id', $selectedHostelId)->first();
        
        $query = User::where('role', 'student')->with(['studentFees', 'roomAssignments.room.hostel', 'studentProfile']);
        
        // Filter by hostel if selected
        if ($selectedHostel) {
            $query->whereHas('roomAssignments.room', function($q) use ($selectedHostel) {
                $q->where('hostel_id', $selectedHostel->id);
            });
        }
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('usn', 'like', "%$search%")
                  ->orWhere('parent_email', 'like', "%$search%")
                  ;
            });
        }
        $students = $query->get();
        
        // Get fee types based on selected hostel or all fee types if no hostel selected
        if ($selectedHostel && is_array($selectedHostel->fees)) {
            $feeTypes = collect($selectedHostel->fees)->pluck('type')->toArray();
        } else {
            $feeTypes = \App\Models\StudentFee::distinct()->pluck('fee_type')->toArray();
        }

        $filename = 'student_fees_status_' . date('Y-m-d_H-i-s') . '.csv';
        
        // Build CSV content as string
        $csvContent = '';
        
                    // Write headers
            $headerRow = ['Student Name', 'USN', 'Email', 'Parent Email'];
        foreach ($feeTypes as $type) {
            $headerRow[] = ucwords(str_replace('_', ' ', $type)) . ' Status';
            $headerRow[] = ucwords(str_replace('_', ' ', $type)) . ' Amount';
        }
        $csvContent .= implode(',', array_map(function($field) {
            return '"' . str_replace('"', '""', $field) . '"';
        }, $headerRow)) . "\n";

        // Write data rows
        foreach ($students as $student) {
            $assignment = $student->roomAssignments->where('status', 'active')->first();
            $hostelName = $assignment && $assignment->room && $assignment->room->hostel ? $assignment->room->hostel->name : '-';
            
                            $row = [
                    $student->name,
                    $student->usn ?? '-',
                    $student->email,
                    $student->studentProfile->father_email ?? $student->parent_email ?? '-'
                ];

            foreach ($feeTypes as $type) {
                $fee = $student->studentFees->where('fee_type', $type)->first();
                $row[] = $fee ? ucfirst($fee->status) : '-';
                $row[] = $fee ? number_format($fee->amount, 2) : '-';
            }

            $csvContent .= implode(',', array_map(function($field) {
                return '"' . str_replace('"', '""', $field) . '"';
            }, $row)) . "\n";
        }

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $hostels = Hostel::where('warden_id', auth()->id())->get();
        $selectedHostelId = $request->input('hostel_id');
        $selectedHostel = $hostels->where('id', $selectedHostelId)->first();
        
        $query = User::where('role', 'student')->with(['studentFees', 'roomAssignments.room.hostel', 'studentProfile']);
        
        // Filter by hostel if selected
        if ($selectedHostel) {
            $query->whereHas('roomAssignments.room', function($q) use ($selectedHostel) {
                $q->where('hostel_id', $selectedHostel->id);
            });
        }
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('usn', 'like', "%$search%")
                  ->orWhere('parent_email', 'like', "%$search%")
                  ;
            });
        }
        $students = $query->get();
        
        // Get fee types based on selected hostel or all fee types if no hostel selected
        if ($selectedHostel && is_array($selectedHostel->fees)) {
            $feeTypes = collect($selectedHostel->fees)->pluck('type')->toArray();
        } else {
            $feeTypes = \App\Models\StudentFee::distinct()->pluck('fee_type')->toArray();
        }

        $data = [
            'students' => $students,
            'feeTypes' => $feeTypes,
            'generatedAt' => now()->format('d M Y, h:i A')
        ];

        $pdf = Pdf::loadView('warden.fees.pdf.student_status', $data);
        
        return $pdf->download('student_fees_status_' . date('Y-m-d_H-i-s') . '.pdf');
    }

    public function exportWord(Request $request)
    {
        $hostels = Hostel::where('warden_id', auth()->id())->get();
        $selectedHostelId = $request->input('hostel_id');
        $selectedHostel = $hostels->where('id', $selectedHostelId)->first();
        
        $query = User::where('role', 'student')->with(['studentFees', 'roomAssignments.room.hostel', 'studentProfile']);
        
        // Filter by hostel if selected
        if ($selectedHostel) {
            $query->whereHas('roomAssignments.room', function($q) use ($selectedHostel) {
                $q->where('hostel_id', $selectedHostel->id);
            });
        }
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('usn', 'like', "%$search%")
                  ->orWhere('parent_email', 'like', "%$search%")
                  ;
            });
        }
        $students = $query->get();
        
        // Get fee types based on selected hostel or all fee types if no hostel selected
        if ($selectedHostel && is_array($selectedHostel->fees)) {
            $feeTypes = collect($selectedHostel->fees)->pluck('type')->toArray();
        } else {
            $feeTypes = \App\Models\StudentFee::distinct()->pluck('fee_type')->toArray();
        }

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Add title
        $section->addText('Student Fees Status Report', ['bold' => true, 'size' => 16]);
        $section->addText('Generated on: ' . now()->format('d M Y, h:i A'), ['size' => 10]);
        $section->addTextBreak(1);

        // Create table
        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);

        // Add header row
        $table->addRow();
        $table->addCell(2000)->addText('Student Name', ['bold' => true]);
        $table->addCell(1500)->addText('USN', ['bold' => true]);
        $table->addCell(2000)->addText('Email', ['bold' => true]);
        $table->addCell(2000)->addText('Parent Email', ['bold' => true]);
        
        foreach ($feeTypes as $type) {
            $table->addCell(1500)->addText(ucwords(str_replace('_', ' ', $type)) . ' Status', ['bold' => true]);
            $table->addCell(1500)->addText(ucwords(str_replace('_', ' ', $type)) . ' Amount', ['bold' => true]);
        }

        // Add data rows
        foreach ($students as $student) {
            $assignment = $student->roomAssignments->where('status', 'active')->first();
            $hostelName = $assignment && $assignment->room && $assignment->room->hostel ? $assignment->room->hostel->name : '-';
            
            $table->addRow();
            $table->addCell(2000)->addText($student->name);
            $table->addCell(1500)->addText($student->usn ?? '-');
            $table->addCell(2000)->addText($student->email);
            $table->addCell(2000)->addText($student->studentProfile->father_email ?? $student->parent_email ?? '-');

            foreach ($feeTypes as $type) {
                $fee = $student->studentFees->where('fee_type', $type)->first();
                $table->addCell(1500)->addText($fee ? ucfirst($fee->status) : '-');
                $table->addCell(1500)->addText($fee ? '₹' . number_format($fee->amount, 2) : '-');
            }
        }

        // Save file
        $filename = 'student_fees_status_' . date('Y-m-d_H-i-s') . '.docx';
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        
        $tempFile = tempnam(sys_get_temp_dir(), 'word_export');
        $objWriter->save($tempFile);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend();
    }

    public function notifyParents(Request $request)
    {
        $studentIds = $request->input('student_ids', []);
        if (empty($studentIds)) {
            return redirect()->back()->with('error', 'No students selected.');
        }
        $students = \App\Models\User::whereIn('id', $studentIds)->with('studentFees')->get();
        $notified = 0;
        foreach ($students as $student) {
            // Try to get parent email from profile first, then fallback to user table
            $parentEmail = $student->studentProfile->father_email ?? $student->studentProfile->mother_email ?? $student->parent_email;
            $pendingFees = $student->studentFees->where('status', 'pending');
            if ($parentEmail && $pendingFees->count()) {
                Notification::route('mail', $parentEmail)
                    ->notify(new PendingFeeNotification($student, $pendingFees));
                $notified++;
            }
        }
        return redirect()->back()->with('success', "$notified parent(s) notified about pending fees.");
    }
} 