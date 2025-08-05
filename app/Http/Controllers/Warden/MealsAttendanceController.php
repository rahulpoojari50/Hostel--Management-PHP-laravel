<?php

namespace App\Http\Controllers\Warden;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hostel;
use App\Models\User;
use App\Models\MealAttendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MealsAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $hostels = Hostel::where('warden_id', Auth::id())->get();
        // For each hostel, count students
        foreach ($hostels as $hostel) {
            $hostel->total_students = User::whereHas('roomAssignments.room', function($q) use ($hostel) {
                $q->where('hostel_id', $hostel->id);
            })->count();
        }
        
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => route('warden.dashboard')],
            ['name' => 'Meals Attendance', 'url' => '']
        ];
        
        // Show list of hostels with Take Attendance button
        return view('warden.meals_attendance_hostels', compact('hostels', 'breadcrumbs'));
    }

    public function hostel($hostelId, Request $request)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($hostelId);
        $selectedHostel = $hostel;
        $date = $request->input('date', now()->toDateString());
        $students = User::whereHas('roomAssignments.room', function($q) use ($hostel) {
            $q->where('hostel_id', $hostel->id);
        })->get();
        $attendance = [];
        foreach (['Breakfast', 'Lunch', 'Snacks', 'Dinner'] as $meal) {
            $attendance[$meal] = MealAttendance::whereIn('student_id', $students->pluck('id'))
                ->where('date', $date)
                ->where('meal_type', $meal)
                ->pluck('status', 'student_id')
                ->toArray();
        }
        $attendanceExists = MealAttendance::whereIn('student_id', $students->pluck('id'))
            ->where('date', $date)
            ->exists();
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => route('warden.dashboard')],
            ['name' => 'Meals Attendance', 'url' => route('warden.meals-attendance.index')],
            ['name' => $selectedHostel->name, 'url' => '']
        ];
        
        return view('warden.meals_attendance', compact('selectedHostel', 'students', 'date', 'attendance', 'attendanceExists', 'breadcrumbs'));
    }

    public function fetchStudents(Request $request)
    {
        $hostelId = $request->input('hostel_id');
        $date = $request->input('date');
        $students = User::whereHas('roomAssignments.room', function($q) use ($hostelId) {
            $q->where('hostel_id', $hostelId);
        })->get();
        $attendance = MealAttendance::where('hostel_id', $hostelId)
            ->where('date', $date)
            ->get()
            ->groupBy('student_id');
        return response()->json([
            'students' => $students,
            'attendance' => $attendance,
        ]);
    }

    public function saveAttendance(Request $request)
    {
        $hostelId = $request->input('hostel_id');
        $date = $request->input('date');
        $studentId = $request->input('student_id');
        $mealType = $request->input('meal_type');
        $status = $request->input('status');
        $attendance = MealAttendance::updateOrCreate(
            [
                'hostel_id' => $hostelId,
                'student_id' => $studentId,
                'date' => $date,
                'meal_type' => $mealType,
            ],
            [
                'status' => $status,
            ]
        );
        return response()->json(['success' => true, 'message' => 'Attendance updated.']);
    }

    public function downloadMealsAttendanceSummaryExcel(Request $request, $hostelId)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $hostel = \App\Models\Hostel::findOrFail($hostelId);
        $students = $hostel->students;
        $studentIds = $students->pluck('id');
        $attendanceRecords = \App\Models\MealAttendance::whereIn('student_id', $studentIds)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();
        $totalMealsServed = $attendanceRecords->count();
        $totalPresent = $attendanceRecords->where('status', 'Taken')->count();
        $totalMissed = $attendanceRecords->whereIn('status', ['Skipped', 'On Leave', 'Holiday'])->count();
        $averageAttendance = $totalMealsServed > 0 ? round(($totalPresent / $totalMealsServed) * 100, 2) : 0;
        // Group by date for highest/lowest attendance day
        $byDate = $attendanceRecords->groupBy('date');
        $attendanceByDay = $byDate->map(function($records) {
            $present = $records->where('status', 'Taken')->count();
            $total = $records->count();
            return $total > 0 ? round(($present / $total) * 100) : 0;
        });
        $highestDay = $attendanceByDay->sortDesc()->keys()->first();
        $lowestDay = $attendanceByDay->sort()->keys()->first();
        $highestDayPercent = $attendanceByDay[$highestDay] ?? 0;
        $lowestDayPercent = $attendanceByDay[$lowestDay] ?? 0;
        $generatedOn = now()->format('d M Y h:i A');
        $period = \Carbon\Carbon::parse($startDate)->format('d') . ' - ' . \Carbon\Carbon::parse($endDate)->format('d M Y');
        $summary = [
            ['Metric', 'Value'],
            ['Total Meals Served', $totalMealsServed],
            ['Total Present', $totalPresent],
            ['Total Missed Meals', $totalMissed],
            ['Average Meal Attendance', $averageAttendance . '%'],
            ['Highest Attendance Day', $highestDay ? $highestDay . ' (' . $highestDayPercent . '%)' : '-'],
            ['Lowest Attendance Day', $lowestDay ? $lowestDay . ' (' . $lowestDayPercent . '%)' : '-'],
            ['Report Duration', $period],
            ['Generated On', $generatedOn],
        ];
        $pdf = \PDF::loadView('warden.pdf.meals_attendance_summary', [
            'hostel' => $hostel,
            'summary' => $summary,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
        $filename = 'meals-attendance-summary-' . $hostel->name . '-' . $startDate . '-to-' . $endDate . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Download meal attendance for a hostel between two dates as CSV
     */
    public function downloadAttendanceCsv(Request $request, $hostelId)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $hostel = \App\Models\Hostel::findOrFail($hostelId);
        $students = $hostel->students;
        $studentIds = $students->pluck('id');
        $dates = [];
        $start = \Carbon\Carbon::parse($dateFrom);
        $end = \Carbon\Carbon::parse($dateTo);
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }
        $mealTypes = ['Breakfast', 'Lunch', 'Snacks', 'Dinner'];
        $attendanceRecords = \App\Models\MealAttendance::whereIn('student_id', $studentIds)
            ->where('hostel_id', $hostelId)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->get();
        $filename = 'meal_attendance_' . $hostel->name . '_' . $dateFrom . '_to_' . $dateTo . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        $columns = ['Name', 'Email', 'Room', 'Hostel', 'Date', 'Breakfast', 'Lunch', 'Snacks', 'Dinner', 'Summary'];
        $rows = [];
        foreach ($students as $student) {
            foreach ($dates as $date) {
                $row = [
                    $student->name,
                    $student->email,
                    optional($student->roomAssignments->first()->room ?? null)->room_number,
                    $hostel->name,
                    $date
                ];
                $presentCount = 0;
                foreach ($mealTypes as $mealType) {
                    $record = $attendanceRecords->first(function($rec) use ($student, $date, $mealType) {
                        return $rec->student_id == $student->id && $rec->date == $date && $rec->meal_type == $mealType;
                    });
                    $status = $record ? $record->status : null;
                    if ($status === 'Taken') $presentCount++;
                    $row[] = $status === 'Taken' ? 'Present' : ($status === 'Skipped' ? 'Absent' : ($status ?? '-'));
                }
                $row[] = $presentCount . '/4 meals present';
                $rows[] = $row;
            }
        }
        return response()->stream(function() use ($columns, $rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, 200, $headers);
    }

    /**
     * Download meal attendance for a hostel between two dates as PDF
     */
    public function downloadAttendancePdf(Request $request, $hostelId)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $hostel = \App\Models\Hostel::findOrFail($hostelId);
        $students = $hostel->students;
        $studentIds = $students->pluck('id');
        $dates = [];
        $start = \Carbon\Carbon::parse($dateFrom);
        $end = \Carbon\Carbon::parse($dateTo);
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }
        $mealTypes = ['Breakfast', 'Lunch', 'Snacks', 'Dinner'];
        $attendanceRecords = \App\Models\MealAttendance::whereIn('student_id', $studentIds)
            ->where('hostel_id', $hostelId)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->get();
        $tableRows = [];
        foreach ($students as $student) {
            foreach ($dates as $date) {
                $row = [
                    'name' => $student->name,
                    'email' => $student->email,
                    'room' => optional($student->roomAssignments->first()->room ?? null)->room_number,
                    'hostel' => $hostel->name,
                    'date' => $date,
                ];
                $presentCount = 0;
                foreach ($mealTypes as $mealType) {
                    $record = $attendanceRecords->first(function($rec) use ($student, $date, $mealType) {
                        return $rec->student_id == $student->id && $rec->date == $date && $rec->meal_type == $mealType;
                    });
                    $status = $record ? $record->status : null;
                    if ($status === 'Taken') $presentCount++;
                    $row[strtolower($mealType)] = $status === 'Taken' ? 'Present' : ($status === 'Skipped' ? 'Absent' : ($status ?? '-'));
                }
                $row['summary'] = $presentCount . '/4 meals present';
                $tableRows[] = $row;
            }
        }
        $pdf = \PDF::loadView('warden.pdf.meal_attendance_table', [
            'hostel' => $hostel,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'tableRows' => $tableRows,
            'mealTypes' => $mealTypes,
        ])->setPaper('a3', 'landscape');
        $filename = 'meal_attendance_' . $hostel->name . '_' . $dateFrom . '_to_' . $dateTo . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Download meal attendance for a hostel between two dates as CSV (full table info)
     */
    public function downloadAttendanceCsvFull(Request $request, $hostelId)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $hostel = \App\Models\Hostel::findOrFail($hostelId);
        $students = $hostel->students;
        $studentIds = $students->pluck('id');
        $dates = [];
        $start = \Carbon\Carbon::parse($dateFrom);
        $end = \Carbon\Carbon::parse($dateTo);
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }
        $mealTypes = ['Breakfast', 'Lunch', 'Snacks', 'Dinner'];
        $attendanceRecords = \App\Models\MealAttendance::whereIn('student_id', $studentIds)
            ->where('hostel_id', $hostelId)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->get();
        $columns = ['Name', 'Email', 'Room', 'Hostel', 'Date'];
        foreach ($mealTypes as $mealType) {
            $columns[] = $mealType;
        }
        $columns[] = 'Summary';
        $rows = [];
        foreach ($students as $student) {
            foreach ($dates as $date) {
                $row = [
                    $student->name,
                    $student->email,
                    optional($student->roomAssignments->first()->room ?? null)->room_number,
                    $hostel->name,
                    $date
                ];
                $presentCount = 0;
                foreach ($mealTypes as $mealType) {
                    $record = $attendanceRecords->first(function($rec) use ($student, $date, $mealType) {
                        return $rec->student_id == $student->id && $rec->date == $date && $rec->meal_type == $mealType;
                    });
                    $status = $record ? $record->status : null;
                    if ($status === 'Taken') $presentCount++;
                    $row[] = $status === 'Taken' ? 'Present' : ($status === 'Skipped' ? 'Absent' : ($status ?? '-'));
                }
                $row[] = $presentCount . '/4 meals present';
                $rows[] = $row;
            }
        }
        $filename = 'meal_attendance_full_' . $hostel->name . '_' . $dateFrom . '_to_' . $dateTo . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        return response()->stream(function() use ($columns, $rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, 200, $headers);
    }
} 