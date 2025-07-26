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

class FeesController extends Controller
{
    public function index(Request $request)
    {
        // For now, just show the Add Fees form for the first hostel managed by the warden
        $hostel = Hostel::where('warden_id', auth()->id())->first();
        return view('warden.fees.index', compact('hostel'));
    }

    public function studentStatus(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = User::where('role', 'student')->with(['studentFees', 'roomAssignments.room.hostel']);
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('parent_email', 'like', "%$search%")
                  ;
            });
        }
        $students = $query->paginate($perPage)->appends($request->only('search', 'per_page'));
        $feeTypes = \App\Models\StudentFee::distinct()->pluck('fee_type')->toArray();
        return view('warden.fees.student_status', compact('students', 'feeTypes'));
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
            $parentEmail = $student->parent_email;
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