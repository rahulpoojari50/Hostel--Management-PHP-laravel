<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\MealAttendance;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user();
        $selectedDate = $request->input('date');
        $query = MealAttendance::where('student_id', $student->id);
        if ($selectedDate) {
            $query->where('date', $selectedDate);
        }
        $attendanceRecords = $query->orderBy('date', 'desc')->get();
        $attendanceByDate = [];
        foreach ($attendanceRecords as $record) {
            $mealType = ucfirst(strtolower($record->meal_type));
            $attendanceByDate[$record->date][$mealType] = $record->status;
        }
        $mealTypes = ['Breakfast', 'Lunch', 'Snacks', 'Dinner'];
        return view('student.attendance', compact('attendanceByDate', 'mealTypes', 'selectedDate'));
    }
} 