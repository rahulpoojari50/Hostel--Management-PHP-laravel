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
        $meals = Meal::with('hostel')
            ->whereIn('hostel_id', $hostelIds)
            ->orderBy('meal_date')
            ->orderBy('meal_type')
            ->get();
            
        $pageTitle = 'Meals Management';
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => route('warden.dashboard')],
            ['name' => 'Meals', 'url' => '']
        ];
        
        return view('warden.meals.index', compact('meals', 'pageTitle', 'breadcrumbs'));
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
        try {
            $warden = Auth::user();
            $hostels = Hostel::where('warden_id', $warden->id)->get();
            $hostelIds = $hostels->pluck('id')->toArray();
            $meal = Meal::whereIn('hostel_id', $hostelIds)->findOrFail($id);
            
            // Log the request data for debugging
            \Log::info('Meal update request data:', $request->all());
            \Log::info('Meal ID: ' . $id);
            \Log::info('Found meal: ' . $meal->toJson());
            
            // Check if this is an attendance update (from show page) or meal update (from edit page)
            $hasAttendanceData = false;
            foreach ($request->all() as $key => $value) {
                if (str_starts_with($key, 'attendance[')) {
                    $hasAttendanceData = true;
                    break;
                }
            }
            
            // Attendance marking (from show page)
            if ($hasAttendanceData) {
                foreach ($request->input('attendance', []) as $studentId => $status) {
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
            
            // Meal update (from edit page) - always process meal data if we're not handling attendance
            $validated = $request->validate([
                'hostel_id' => 'required|in:' . implode(',', $hostelIds),
                'meal_type' => 'required|in:breakfast,lunch,snacks,dinner',
                'meal_date' => 'required|date',
                'menu_description' => 'nullable|string',
            ]);
            
            \Log::info('Validated meal data:', $validated);
            
            // Ensure menu_description is not null if it's empty
            if (empty($validated['menu_description'])) {
                $validated['menu_description'] = null;
            }
            
            $meal->update($validated);
            return redirect()->route('warden.meals.index')->with('success', 'Meal updated successfully.');
            
        } catch (\Exception $e) {
            \Log::error('Meal update error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'An error occurred while updating the meal. Please try again.');
        }
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
