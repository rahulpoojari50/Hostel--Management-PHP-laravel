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
            // Student has an active room assignment
            $hostel = $assignment->room->hostel;
            $meals = Meal::where('hostel_id', $hostel->id)
                ->where('meal_date', '>=', now()->toDateString())
                ->orderBy('meal_date')
                ->orderBy('meal_type')
                ->get();
        } elseif ($application && $application->isApproved() && $application->hostel) {
            // Student has an approved application but no room assignment yet
            $hostel = $application->hostel;
            $meals = Meal::where('hostel_id', $hostel->id)
                ->where('meal_date', '>=', now()->toDateString())
                ->orderBy('meal_date')
                ->orderBy('meal_type')
                ->get();
        }
        // If no hostel is found, $hostel will remain null and no menu will be displayed
        // Group meals by date, then by meal_type
        $groupedMeals = $meals->groupBy('meal_date')->map(function($mealsForDate) {
            return $mealsForDate->keyBy('meal_type');
        });
        // Get all meal types present in the data (for columns)
        $mealTypes = $meals->pluck('meal_type')->unique()->values();

        // Prepare weekly menu from hostel->menu (if available)
        $weeklyMenu = [];
        $daysOfWeek = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']; // Match warden form order
        $menuMealTypes = ['breakfast','lunch','snacks','dinner'];
        if ($hostel && is_array($hostel->menu)) {
            foreach ($daysOfWeek as $day) {
                foreach ($menuMealTypes as $type) {
                    $weeklyMenu[$day][$type] = $hostel->menu[$day][$type] ?? '-';
                }
            }
        }
        
        // Prepare meal menu from hostel->meal_menu (if available) - New structure with days as rows
        $mealMenu = [];
        if ($hostel && is_array($hostel->meal_menu)) {
            $mealMenu = $hostel->meal_menu;
        }

        return view('student.dashboard', compact('application', 'assignment', 'groupedMeals', 'mealTypes', 'weeklyMenu', 'daysOfWeek', 'menuMealTypes', 'mealMenu'));
    }
}
