<?php

namespace App\Http\Controllers\Warden;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hostel;
use App\Models\User;
use App\Models\MealAttendance;
use Illuminate\Support\Facades\Auth;

class MealAttendanceController extends Controller
{
    public function index(Request $request, $hostelId = null)
    {
        $warden = Auth::user();
        $hostels = Hostel::where('warden_id', $warden->id)->get();
        $selectedHostel = $hostelId ? $hostels->where('id', $hostelId)->first() : null;
        $date = $request->input('date', now()->toDateString());
        $editMode = $request->boolean('edit', false);

        if (!$hostelId) {
            // Show list of hostels with Take Attendance button
            return view('warden.meals_attendance_hostels', compact('hostels'));
        }

        $students = $selectedHostel
            ? \App\Models\User::whereHas('roomAssignments', function($q) use ($selectedHostel) {
                $q->where('status', 'active')
                  ->whereHas('room', function($qr) use ($selectedHostel) {
                      $qr->where('hostel_id', $selectedHostel->id);
                  });
            })->get()
            : collect();

        // Fetch attendance for all students for the date
        $attendance = [];
        foreach (['Breakfast', 'Lunch', 'Snacks', 'Dinner'] as $meal) {
            $attendance[$meal] = MealAttendance::whereIn('student_id', $students->pluck('id'))
                ->where('date', $date)
                ->where('meal_type', $meal)
                ->pluck('status', 'student_id')
                ->toArray();
        }
        // Fetch remarks for all students for the date (any meal)
        $attendance['remarks'] = MealAttendance::whereIn('student_id', $students->pluck('id'))
            ->where('date', $date)
            ->pluck('remarks', 'student_id')
            ->toArray();

        // Check if attendance already exists for today for any student
        $attendanceExists = MealAttendance::whereIn('student_id', $students->pluck('id'))
            ->where('date', $date)
            ->exists();

        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => route('warden.dashboard')],
            ['name' => 'Meals Attendance', 'url' => route('warden.meals-attendance.index')],
            ['name' => $selectedHostel ? $selectedHostel->name : 'Select Hostel', 'url' => '']
        ];
        
        return view('warden.meals_attendance', compact('hostels', 'selectedHostel', 'date', 'students', 'attendance', 'attendanceExists', 'editMode', 'breadcrumbs'));
    }

    public function store(Request $request, $hostelId)
    {
        try {
            $warden = Auth::user();
            $date = $request->input('date');
            $data = $request->input('status', []); // Fix: use 'status' as in the form
            $remarks = $request->input('remarks', []);

            foreach ($data as $studentId => $meals) {
                foreach (['Breakfast', 'Lunch', 'Snacks', 'Dinner'] as $meal) {
                    // Accept both lowercase and correct case from form, but always save correct case
                    $status = $meals[$meal] ?? $meals[ucfirst(strtolower($meal))] ?? $meals[strtolower($meal)] ?? null;
                    if ($status) {
                        MealAttendance::updateOrCreate(
                            [
                                'student_id' => $studentId,
                                'date' => $date,
                                'meal_type' => $meal, // Always save as 'Breakfast', 'Lunch', etc.
                            ],
                            [
                                'status' => $status,
                                'marked_by' => $warden->id,
                                'hostel_id' => $hostelId,
                                'remarks' => $remarks[$studentId] ?? null,
                            ]
                        );
                    }
                }
            }
            // Redirect to view attendance for the same date (no take/edit param)
            return redirect()->route('warden.meals-attendance.index', [$hostelId, 'date' => $date])
                ->with('success', 'Attendance saved successfully!');
        } catch (\Exception $e) {
            \Log::error('Attendance error: ' . $e->getMessage());
            return back()->with('error', 'Error updating attendance: ' . $e->getMessage());
        }
    }

    public function downloadCsv(Request $request, $hostelId)
    {
        $date = $request->input('date', now()->toDateString());
        $hostel = \App\Models\Hostel::findOrFail($hostelId);
        $students = \App\Models\User::whereHas('roomAssignments.room', function($q) use ($hostel) {
            $q->where('hostel_id', $hostel->id);
        })->get();
        $attendance = [];
        foreach (['Breakfast','Lunch','Snacks','Dinner'] as $meal) {
            $attendance[$meal] = \App\Models\MealAttendance::where('hostel_id', $hostelId)
                ->where('date', $date)
                ->where('meal_type', $meal)
                ->pluck('status', 'student_id')
                ->toArray();
        }
        $filename = 'attendance-' . $date . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $columns = ['Student Name', 'Room No', 'Breakfast', 'Lunch', 'Snacks', 'Dinner'];
        $callback = function() use ($students, $attendance, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($students as $student) {
                $row = [
                    $student->name,
                    optional($student->roomAssignments->first()->room)->room_number ?? '-',
                ];
                foreach (['Breakfast','Lunch','Snacks','Dinner'] as $meal) {
                    $status = $attendance[$meal][$student->id] ?? '';
                    $short = $status === 'Taken' ? 'P' : ($status === 'Skipped' ? 'A' : ($status === 'On Leave' ? 'L' : ($status === 'Holiday' ? 'H' : '')));
                    $row[] = $short;
                }
                fputcsv($file, $row);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
} 