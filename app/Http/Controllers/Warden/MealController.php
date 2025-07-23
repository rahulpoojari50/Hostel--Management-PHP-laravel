<?php

namespace App\Http\Controllers\Warden;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Meal;
use App\Models\Hostel;
use App\Models\User;
use App\Models\MealAttendance;
use Illuminate\Support\Facades\Auth;

class MealController extends Controller
{
    public function index()
    {
        $warden = Auth::user();
        $hostelIds = Hostel::where('warden_id', $warden->id)->pluck('id');
        // Filter meals for the current week (Monday to Sunday)
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $meals = Meal::with('hostel')
            ->whereIn('hostel_id', $hostelIds)
            ->whereBetween('meal_date', [$startOfWeek, $endOfWeek])
            ->orderBy('meal_date')
            ->orderBy('meal_type')
            ->get();
        return view('warden.meals.index', compact('meals'));
    }

    public function create()
    {
        $warden = Auth::user();
        $hostels = Hostel::where('warden_id', $warden->id)->get();
        return view('warden.meals.create', compact('hostels'));
    }

    public function store(Request $request)
    {
        $warden = Auth::user();
        $hostelIds = Hostel::where('warden_id', $warden->id)->pluck('id')->toArray();
        $validated = $request->validate([
            'hostel_id' => 'required|in:' . implode(',', $hostelIds),
            'meal_type' => 'required|in:breakfast,lunch,snacks,dinner',
            'meal_date' => 'required|date',
            'menu_description' => 'nullable|string',
        ]);
        $meal = Meal::create($validated);
        // Optionally, create attendance records for all students in the hostel
        $students = User::where('role', 'student')->get();
        foreach ($students as $student) {
            MealAttendance::firstOrCreate([
                'student_id' => $student->id,
                'meal_id' => $meal->id,
            ], [
                'attendance_status' => 'absent',
                'marked_by' => null,
            ]);
        }
        return redirect()->route('warden.meals.index')->with('success', 'Meal created successfully.');
    }

    public function show($id)
    {
        $warden = Auth::user();
        $hostelIds = Hostel::where('warden_id', $warden->id)->pluck('id');
        $meal = Meal::with('hostel')->whereIn('hostel_id', $hostelIds)->findOrFail($id);
        $students = User::where('role', 'student')->get();
        $attendance = MealAttendance::where('meal_id', $meal->id)->pluck('attendance_status', 'student_id')->toArray();
        $presentCount = MealAttendance::where('meal_id', $meal->id)->where('attendance_status', 'present')->count();
        $absentCount = MealAttendance::where('meal_id', $meal->id)->where('attendance_status', 'absent')->count();
        $totalCount = $presentCount + $absentCount;
        $attendancePercent = $totalCount > 0 ? round(($presentCount / $totalCount) * 100, 2) : 0;
        return view('warden.meals.show', compact('meal', 'students', 'attendance', 'presentCount', 'absentCount', 'totalCount', 'attendancePercent'));
    }

    public function edit($id)
    {
        $warden = Auth::user();
        $hostels = Hostel::where('warden_id', $warden->id)->get();
        $hostelIds = $hostels->pluck('id');
        $meal = Meal::whereIn('hostel_id', $hostelIds)->findOrFail($id);
        return view('warden.meals.edit', compact('meal', 'hostels'));
    }

    public function update(Request $request, $id)
    {
        $warden = Auth::user();
        $hostels = Hostel::where('warden_id', $warden->id)->get();
        $hostelIds = $hostels->pluck('id')->toArray();
        $meal = Meal::whereIn('hostel_id', $hostelIds)->findOrFail($id);
        // Attendance marking
        if ($request->has('attendance')) {
            foreach ($request->input('attendance') as $studentId => $status) {
                $attendance = MealAttendance::firstOrNew([
                    'student_id' => $studentId,
                    'meal_id' => $meal->id,
                ]);
                $attendance->attendance_status = $status;
                $attendance->marked_at = now();
                $attendance->marked_by = $warden->id;
                $attendance->save();
            }
            return redirect()->route('warden.meals.show', $meal)->with('success', 'Attendance updated.');
        }
        // Meal update
        $validated = $request->validate([
            'hostel_id' => 'required|in:' . implode(',', $hostelIds),
            'meal_type' => 'required|in:breakfast,lunch,snacks,dinner',
            'meal_date' => 'required|date',
            'menu_description' => 'nullable|string',
        ]);
        $meal->update($validated);
        return redirect()->route('warden.meals.index')->with('success', 'Meal updated successfully.');
    }

    public function destroy($id)
    {
        $warden = Auth::user();
        $hostelIds = Hostel::where('warden_id', $warden->id)->pluck('id');
        $meal = Meal::whereIn('hostel_id', $hostelIds)->findOrFail($id);
        $meal->delete();
        return redirect()->route('warden.meals.index')->with('success', 'Meal deleted successfully.');
    }
}
