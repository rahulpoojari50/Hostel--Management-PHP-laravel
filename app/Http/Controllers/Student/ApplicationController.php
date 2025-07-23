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
        // Prevent multiple applications
        $existing = RoomApplication::where('student_id', $student->id)->where('status', '!=', 'rejected')->first();
        if ($existing) {
            return redirect()->route('student.dashboard')->with('error', 'You already have a pending or approved application.');
        }
        return view('student.applications.create', compact('hostel', 'student'));
    }

    public function store(Request $request, $hostelId)
    {
        $hostel = Hostel::findOrFail($hostelId);
        $student = Auth::user();
        $validated = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'amount' => 'required|numeric|min:0',
        ]);
        // Prevent multiple applications
        $existing = RoomApplication::where('student_id', $student->id)->where('status', '!=', 'rejected')->first();
        if ($existing) {
            return redirect()->route('student.dashboard')->with('error', 'You already have a pending or approved application.');
        }
        $application = RoomApplication::create([
            'student_id' => $student->id,
            'hostel_id' => $hostel->id,
            'room_type_id' => $validated['room_type_id'],
            'amount' => $validated['amount'],
            'status' => 'pending',
            'application_date' => now(),
        ]);
        // Save student fees
        $feesPaidNow = json_decode($request->input('fees_paid_now', '[]'), true);
        $feesPayLater = json_decode($request->input('fees_pay_later', '[]'), true);
        foreach ($feesPaidNow as $fee) {
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
        foreach ($feesPayLater as $fee) {
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
        return redirect()->route('student.applications.receipt', $application->id)->with('success', 'Application submitted!');
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
