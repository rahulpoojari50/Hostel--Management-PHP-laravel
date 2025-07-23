<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RoomApplication;
use App\Models\RoomAssignment;
use App\Models\Meal;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $student = Auth::user();
        $application = RoomApplication::with(['hostel', 'roomType'])
            ->where('student_id', $student->id)
            ->latest('application_date')
            ->first();
        $assignment = RoomAssignment::with(['room.hostel', 'room.roomType'])
            ->where('student_id', $student->id)
            ->where('status', 'active')
            ->latest('assigned_date')
            ->first();
        $meals = collect();
        $hostel = null;
        if ($assignment && $assignment->room && $assignment->room->hostel) {
            $hostel = $assignment->room->hostel;
            $meals = Meal::where('hostel_id', $hostel->id)
                ->where('meal_date', '>=', now()->toDateString())
                ->orderBy('meal_date')
                ->orderBy('meal_type')
                ->get();
        } elseif ($application && $application->isApproved() && $application->hostel) {
            $hostel = $application->hostel;
            $meals = Meal::where('hostel_id', $hostel->id)
                ->where('meal_date', '>=', now()->toDateString())
                ->orderBy('meal_date')
                ->orderBy('meal_type')
                ->get();
        }
        // Group meals by date, then by meal_type
        $groupedMeals = $meals->groupBy('meal_date')->map(function($mealsForDate) {
            return $mealsForDate->keyBy('meal_type');
        });
        // Get all meal types present in the data (for columns)
        $mealTypes = $meals->pluck('meal_type')->unique()->values();

        // Prepare weekly menu from hostel->menu (if available)
        $weeklyMenu = [];
        $daysOfWeek = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
        $menuMealTypes = ['breakfast','lunch','snacks','dinner'];
        if ($hostel && is_array($hostel->menu)) {
            foreach ($daysOfWeek as $day) {
                foreach ($menuMealTypes as $type) {
                    $weeklyMenu[$day][$type] = $hostel->menu[$day][$type] ?? '-';
                }
            }
        }
        return view('student.dashboard', compact('application', 'assignment', 'groupedMeals', 'mealTypes', 'weeklyMenu', 'daysOfWeek', 'menuMealTypes'));
    }
}
