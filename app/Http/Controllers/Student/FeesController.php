<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentFee;

class FeesController extends Controller
{
    public function paid(Request $request)
    {
        $paidFees = StudentFee::where('student_id', auth()->id())->where('status', 'paid')->get();
        return view('student.fees.paid', compact('paidFees'));
    }

    public function pending(Request $request)
    {
        $pendingFees = StudentFee::where('student_id', auth()->id())->where('status', 'pending')->get();
        return view('student.fees.pending', compact('pendingFees'));
    }

    public function pay($id)
    {
        $fee = StudentFee::where('student_id', auth()->id())->where('id', $id)->where('status', 'pending')->firstOrFail();
        $fee->status = 'paid';
        $fee->paid_at = now();
        $fee->save();
        return redirect()->route('student.fees.paid')->with('success', 'Fee paid successfully!');
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