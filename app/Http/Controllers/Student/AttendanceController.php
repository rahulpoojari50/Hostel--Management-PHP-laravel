<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\MealAttendance;
use App\Models\HostelAttendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user();
        $selectedDate = $request->input('date');
        $attendanceType = $request->input('type', 'all'); // all, meal, hostel
        $perPage = $request->input('per_page', 20);
        
        // Get meal attendance records
        $mealQuery = MealAttendance::where('student_id', $student->id);
        if ($selectedDate) {
            $mealQuery->where('date', $selectedDate);
        }
        $mealRecords = $mealQuery->orderBy('date', 'desc')->get();
        
        // Get hostel attendance records
        $hostelQuery = HostelAttendance::where('student_id', $student->id);
        if ($selectedDate) {
            $hostelQuery->where('date', $selectedDate);
        }
        $hostelRecords = $hostelQuery->orderBy('date', 'desc')->get();
        
        // Process meal attendance by date
        $mealAttendanceByDate = [];
        foreach ($mealRecords as $record) {
            $mealType = ucfirst(strtolower($record->meal_type));
            $mealAttendanceByDate[$record->date][$mealType] = $record->status;
        }
        
        // Process hostel attendance by date
        $hostelAttendanceByDate = [];
        foreach ($hostelRecords as $record) {
            $hostelAttendanceByDate[$record->date] = [
                'status' => $record->status,
                'remarks' => $record->remarks
            ];
        }
        
        // Combine all dates based on type filter
        $allDates = [];
        if ($attendanceType == 'all' || $attendanceType == 'meal') {
            $allDates = array_merge($allDates, array_keys($mealAttendanceByDate));
        }
        if ($attendanceType == 'all' || $attendanceType == 'hostel') {
            $allDates = array_merge($allDates, array_keys($hostelAttendanceByDate));
        }
        $allDates = array_unique($allDates);
        rsort($allDates);
        
        // Filter dates based on type
        if ($attendanceType == 'meal') {
            $allDates = array_intersect($allDates, array_keys($mealAttendanceByDate));
        } elseif ($attendanceType == 'hostel') {
            $allDates = array_intersect($allDates, array_keys($hostelAttendanceByDate));
        }
        
        // Paginate the results
        $currentPage = $request->input('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedDates = array_slice($allDates, $offset, $perPage);
        
        $mealTypes = ['Breakfast', 'Lunch', 'Snacks', 'Dinner'];
        
        // Calculate attendance statistics
        $totalMealRecords = $mealRecords->count();
        $totalHostelRecords = $hostelRecords->count();
        $presentMealRecords = $mealRecords->where('status', 'Taken')->count();
        $presentHostelRecords = $hostelRecords->where('status', 'Taken')->count();
        
        $mealAttendancePercentage = $totalMealRecords > 0 ? round(($presentMealRecords / $totalMealRecords) * 100, 2) : 0;
        $hostelAttendancePercentage = $totalHostelRecords > 0 ? round(($presentHostelRecords / $totalHostelRecords) * 100, 2) : 0;
        
        return view('student.attendance', compact(
            'mealAttendanceByDate',
            'hostelAttendanceByDate', 
            'paginatedDates',
            'mealTypes',
            'selectedDate',
            'attendanceType',
            'totalMealRecords',
            'totalHostelRecords',
            'presentMealRecords',
            'presentHostelRecords',
            'mealAttendancePercentage',
            'hostelAttendancePercentage',
            'allDates'
        ));
    }
} 