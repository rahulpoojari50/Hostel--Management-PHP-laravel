<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentFee;
use App\Models\RoomApplication;

class FeesController extends Controller
{
    public function paid(Request $request)
    {
        $paidFees = StudentFee::where('student_id', auth()->id())->where('status', 'paid')->get();
        return view('student.fees.paid', compact('paidFees'));
    }

    public function pending(Request $request)
    {
        // Get all fees for the student and group by fee type to handle duplicates
        $allFees = StudentFee::where('student_id', auth()->id())->get();
        
        // Group fees by type and get the latest status for each
        $groupedFees = $allFees->groupBy('fee_type')->map(function($fees) {
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
        })->values();
        
        $pendingFees = $groupedFees->where('status', 'pending');
        $paidFees = $groupedFees->where('status', 'paid');
        
        return view('student.fees.pending', compact('groupedFees', 'pendingFees', 'paidFees'));
    }

    public function pay($id)
    {
        $fee = StudentFee::where('student_id', auth()->id())->where('id', $id)->firstOrFail();
        $application = \App\Models\RoomApplication::where('student_id', auth()->id())->latest()->first();
        if ($application) {
            // Set from_fees in session so ApplicationController can detect
            session(['from_fees' => true]);
            // Redirect to application page, pass fee_type as query param
            return redirect()->route('student.applications.create', [$application->hostel_id, 'fee_type' => $fee->fee_type]);
        } else {
            // If no application exists, redirect to hostels index
            return redirect()->route('student.hostels.index')->with('success', 'Please apply for a hostel first.');
        }
    }

    public function receipt($id)
    {
        $fee = StudentFee::with(['student', 'hostel', 'application'])->where('student_id', auth()->id())->where('id', $id)->where('status', 'paid')->firstOrFail();
        return view('student.fees.receipt', compact('fee'));
    }

    public function downloadReceipt($id)
    {
        $fee = StudentFee::with(['student', 'hostel', 'application'])->where('student_id', auth()->id())->where('id', $id)->where('status', 'paid')->firstOrFail();
        $pdf = \PDF::loadView('student.fees.receipt', compact('fee'));
        $filename = 'fee-receipt-' . $fee->id . '.pdf';
        return $pdf->download($filename);
    }
} 