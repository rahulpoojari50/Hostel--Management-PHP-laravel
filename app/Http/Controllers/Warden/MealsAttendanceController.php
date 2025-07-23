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
        // Show list of hostels with Take Attendance button
        return view('warden.meals_attendance_hostels', compact('hostels'));
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
        return view('warden.meals_attendance', compact('selectedHostel', 'students', 'date', 'attendance', 'attendanceExists'));
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
} 