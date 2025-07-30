<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hostel;
use App\Models\RoomType;
use App\Models\RoomApplication;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentFee;

class ApplicationController extends Controller
{
    public function create($hostelId)
    {
        $hostel = Hostel::with('roomTypes')->findOrFail($hostelId);
        $student = Auth::user();
        
        // Get existing application if any
        $existingApplication = RoomApplication::where('student_id', $student->id)
            ->where('hostel_id', $hostelId)
            ->where('status', '!=', 'rejected')
            ->first();
            
        // Get existing fees for this student and hostel
        $existingFees = StudentFee::where('student_id', $student->id)
            ->where('hostel_id', $hostelId)
            ->get()
            ->groupBy('fee_type')
            ->map(function($fees) {
                // If any fee is paid, consider it paid, otherwise pending
                $hasPaid = $fees->where('status', 'paid')->count() > 0;
                $latestFee = $fees->sortByDesc('created_at')->first();
                
                return [
                    'id' => $latestFee->id,
                    'fee_type' => $latestFee->fee_type,
                    'amount' => $latestFee->amount,
                    'status' => $hasPaid ? 'paid' : 'pending',
                    'paid_at' => $hasPaid ? $fees->where('status', 'paid')->first()->paid_at : null,
                    'hostel_id' => $latestFee->hostel_id,
                    'application_id' => $latestFee->application_id,
                ];
            });
            
        // If there's an existing application, redirect to dashboard (unless coming from fees page)
        if ($existingApplication && !session('from_fees')) {
            return redirect()->route('student.dashboard')->with('error', 'You already have a pending or approved application for this hostel.');
        }
        
        // Determine if room type should be disabled (when coming from fees page)
        $disableRoomType = session('from_fees') && $existingApplication;
        
        return view('student.applications.create', compact('hostel', 'student', 'existingFees', 'existingApplication', 'disableRoomType'));
    }

    public function store(Request $request, $hostelId)
    {
        $hostel = Hostel::findOrFail($hostelId);
        $student = Auth::user();
        
        // Check if this is an update to existing application
        $existingApplication = RoomApplication::where('student_id', $student->id)
            ->where('hostel_id', $hostelId)
            ->where('status', '!=', 'rejected')
            ->first();
            
        // If room type is disabled (coming from fees page), use existing application's room type
        if (session('from_fees') && $existingApplication) {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:0',
            ]);
            $roomTypeId = $existingApplication->room_type_id;
        } else {
            $validated = $request->validate([
                'room_type_id' => 'required|exists:room_types,id',
                'amount' => 'required|numeric|min:0',
            ]);
            $roomTypeId = $validated['room_type_id'];
        }
            
        if ($existingApplication) {
            // Update existing application
            $application = $existingApplication;
            $application->update([
                'room_type_id' => $roomTypeId,
                'amount' => $validated['amount'],
            ]);
        } else {
            // Create new application
            $application = RoomApplication::create([
                'student_id' => $student->id,
                'hostel_id' => $hostel->id,
                'room_type_id' => $roomTypeId,
                'amount' => $validated['amount'],
                'status' => 'pending',
                'application_date' => now(),
            ]);
        }
        
        // Handle fee payments
        $feesToPay = json_decode($request->input('fees_to_pay', '[]'), true);
        $feesToPayLater = json_decode($request->input('fees_pay_later', '[]'), true);
        
        // Process fees to pay now
        foreach ($feesToPay as $fee) {
            $existingFee = StudentFee::where('student_id', $student->id)
                ->where('hostel_id', $hostel->id)
                ->where('fee_type', $fee['type'])
                ->first();
                
            if ($existingFee) {
                // Update existing fee to paid
                $existingFee->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);
            } else {
                // Create new paid fee
                StudentFee::create([
                    'student_id' => $student->id,
                    'hostel_id' => $hostel->id,
                    'application_id' => $application->id,
                    'fee_type' => $fee['type'],
                    'amount' => $fee['amount'],
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);
            }
        }
        
        // Process fees to pay later - only create if no fee exists for this type
        foreach ($feesToPayLater as $fee) {
            $existingFee = StudentFee::where('student_id', $student->id)
                ->where('hostel_id', $hostel->id)
                ->where('fee_type', $fee['type'])
                ->first();
                
            if (!$existingFee) {
                // Create new pending fee only if it doesn't exist
                StudentFee::create([
                    'student_id' => $student->id,
                    'hostel_id' => $hostel->id,
                    'application_id' => $application->id,
                    'fee_type' => $fee['type'],
                    'amount' => $fee['amount'],
                    'status' => 'pending',
                    'paid_at' => null,
                ]);
            }
        }
        
        // Clear the from_fees session
        session()->forget('from_fees');
        
        return redirect()->route('student.applications.receipt', $application->id)->with('success', 'Application submitted successfully!');
    }

    public function receipt($applicationId)
    {
        $application = RoomApplication::with(['hostel', 'roomType', 'student'])->findOrFail($applicationId);
        $student = Auth::user();
        if ($application->student_id !== $student->id) {
            abort(403);
        }
        return view('student.applications.receipt', compact('application', 'student'));
    }
}
