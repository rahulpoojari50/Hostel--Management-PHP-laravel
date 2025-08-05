<?php

namespace App\Http\Controllers\Warden;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hostel;
use Illuminate\Support\Facades\Auth;
use App\Models\MealAttendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\RoomApplication;
use App\Models\HostelAttendance;
use App\Notifications\StudentAbsentNotification;
use Illuminate\Support\Facades\Notification;

class HostelController extends Controller
{
    public function __construct()
    {
        view()->composer('*', function ($view) {
            if (auth()->check() && auth()->user()->role === 'warden') {
                $view->with('sidebarHostels', \App\Models\Hostel::where('warden_id', auth()->id())->get());
            }
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hostels = Hostel::where('warden_id', Auth::id())->get();
        $pageTitle = 'Hostels Management';
      
        return view('warden.hostels', compact('hostels', 'pageTitle', 'breadcrumbs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('warden.hostels_create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:boys,girls,mixed',
            'address' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);
        $validated['warden_id'] = Auth::id();
        Hostel::create($validated);
        return redirect()->route('warden.hostels.index')->with('success', 'Hostel created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $hostel = Hostel::where('warden_id', Auth::id())
            ->with([
                'warden',
                'roomTypes.rooms.roomAssignments.student',
                'rooms.roomType',
                'rooms.roomAssignments.student',
                'roomApplications.student',
                'meals',
            ])->findOrFail($id);
        // Get all students allotted rooms in this hostel, paginated
        $students = \App\Models\User::whereHas('roomAssignments.room', function($q) use ($hostel) {
            $q->where('hostel_id', $hostel->id);
        })->paginate(10);
        
        $pageTitle = 'Hostel Details';
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => url('/warden/dashboard')],
            
            ['name' => 'Hostel Details', 'url' => '']
        ];
        
        return view('warden.hostels_show', compact('hostel', 'students', 'pageTitle', 'breadcrumbs'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($id);
        
        $pageTitle = 'Edit Hostel';
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => url('/warden/dashboard')],
            
            ['name' => 'Edit Hostel', 'url' => '']
        ];
        
        return view('warden.hostels_edit', compact('hostel', 'pageTitle', 'breadcrumbs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:boys,girls,mixed',
            'address' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            // Keep other fields if you use them elsewhere
            'room_1_share' => 'nullable|integer|min:0',
            'room_2_share' => 'nullable|integer|min:0',
            'room_3_share' => 'nullable|integer|min:0',
            'menu' => 'nullable|array',
            'price_1_share' => 'nullable|numeric|min:0',
            'price_2_share' => 'nullable|numeric|min:0',
            'price_3_share' => 'nullable|numeric|min:0',
        ]);
        // Update main hostel fields
        $hostel->update($validated);
        // If you want to keep the old logic for room shares, menu, etc.:
        $hostel->room_1_share = $request->input('room_1_share', 0);
        $hostel->room_2_share = $request->input('room_2_share', 0);
        $hostel->room_3_share = $request->input('room_3_share', 0);
        $hostel->menu = $request->input('menu', []);
        $hostel->description = $request->input('description', '');
        $hostel->save();
        // Sync room_types table (keep as is)
        $roomTypes = [
            ['type' => '1-share', 'capacity' => 1, 'total_rooms' => $hostel->room_1_share, 'price_per_month' => $request->input('price_1_share', 0)],
            ['type' => '2-share', 'capacity' => 2, 'total_rooms' => $hostel->room_2_share, 'price_per_month' => $request->input('price_2_share', 0)],
            ['type' => '3-share', 'capacity' => 3, 'total_rooms' => $hostel->room_3_share, 'price_per_month' => $request->input('price_3_share', 0)],
        ];
        foreach ($roomTypes as $rt) {
            if ($rt['total_rooms'] > 0) {
                $hostel->roomTypes()->updateOrCreate(
                    ['type' => $rt['type']],
                    [
                        'capacity' => $rt['capacity'],
                        'total_rooms' => $rt['total_rooms'],
                        'price_per_month' => $rt['price_per_month'],
                    ]
                );
            } else {
                $hostel->roomTypes()->where('type', $rt['type'])->delete();
            }
        }
        return redirect()->back()->with('success', 'Hostel updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($id);
        $hostel->delete();
        return redirect()->route('warden.hostels.index')->with('success', 'Hostel deleted successfully.');
    }

    /**
     * Update room counts for a hostel.
     */
    public function updateRoomCounts(Request $request, $id)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($id);
        $hostel->room_1_share = $request->input('room_1_share', 0);
        $hostel->room_2_share = $request->input('room_2_share', 0);
        $hostel->room_3_share = $request->input('room_3_share', 0);
        $hostel->save();
        return redirect()->back()->with('success', 'Room counts updated.');
    }

    /**
     * Update weekly menu for a hostel.
     */
    public function updateMenu(Request $request, $id)
    {
        try {
            // Debug logging
            if ($request->has('debug')) {
                \Log::info('Menu update request received', [
                    'hostel_id' => $id,
                    'warden_id' => Auth::id(),
                    'menu_data' => $request->input('menu'),
                    'all_data' => $request->all(),
                    'method' => $request->method(),
                    'url' => $request->url(),
                    'form_submitted' => $request->has('form_submitted'),
                    'csrf_token' => $request->has('_token'),
                    'headers' => $request->headers->all()
                ]);
            }
            
            $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($id);
            $menu = $request->input('menu', []);
            
            // Debug: Log the raw menu data
            if ($request->has('debug')) {
                \Log::info('Raw menu data received', [
                    'menu' => $menu,
                    'menu_type' => gettype($menu),
                    'menu_count' => is_array($menu) ? count($menu) : 'not array',
                    'request_all' => $request->all(),
                    'request_inputs' => $request->input()
                ]);
            }
            
            // Validate that menu data is provided
            if (empty($menu)) {
                $errorMessage = 'No menu data provided. Please fill in at least one menu item.';
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ]);
                }
                return redirect()->back()->with('error', $errorMessage);
            }
            
            // Validate menu structure
            if (!is_array($menu)) {
                $errorMessage = 'Invalid menu data format. Please try again.';
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ]);
                }
                return redirect()->back()->with('error', $errorMessage);
            }
            
            // Clean up empty values and ensure proper structure
            $cleanedMenu = [];
            $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
            $mealTypes = ['breakfast','lunch','snacks','dinner'];
            
            foreach ($days as $day) {
                $cleanedMenu[$day] = [];
                foreach ($mealTypes as $mealType) {
                    $value = trim($menu[$day][$mealType] ?? '');
                    $cleanedMenu[$day][$mealType] = $value;
                }
            }
            
            // Debug: Log the cleaned menu data
            if ($request->has('debug')) {
                \Log::info('Cleaned menu data', [
                    'cleaned_menu' => $cleanedMenu,
                    'days_processed' => count($days),
                    'meal_types_processed' => count($mealTypes)
                ]);
            }
            
            // Check if at least one menu item is filled
            $hasContent = false;
            foreach ($cleanedMenu as $day => $meals) {
                foreach ($meals as $meal => $description) {
                    if (!empty($description)) {
                        $hasContent = true;
                        break 2;
                    }
                }
            }
            
            if (!$hasContent) {
                $errorMessage = 'Please fill in at least one menu item before updating.';
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ]);
                }
                return redirect()->back()->with('error', $errorMessage);
            }
            
            // Save the menu to the hostel
            $hostel->menu = $cleanedMenu;
            $hostel->save();
            
            // Debug logging after save
            if ($request->has('debug')) {
                \Log::info('Menu saved successfully', [
                    'hostel_id' => $hostel->id,
                    'saved_menu' => $hostel->menu,
                    'cleaned_menu' => $cleanedMenu
                ]);
            }
            
            // Update existing meals in the database to reflect the new menu
            $mealsUpdated = 0;
            $existingMeals = \App\Models\Meal::where('hostel_id', $hostel->id)
                ->where('meal_date', '>=', \Carbon\Carbon::today())
                ->get();
            
            foreach ($existingMeals as $meal) {
                // Get the day name based on the meal date
                $dayIndex = $meal->meal_date->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.
                $dayName = $days[$dayIndex];
                $newMenuDesc = $cleanedMenu[$dayName][$meal->meal_type] ?? null;
                
                // Only update if the menu description has changed
                if ($newMenuDesc !== $meal->menu_description) {
                    $meal->menu_description = $newMenuDesc;
                    $meal->save();
                    $mealsUpdated++;
                }
            }
            
            // Count how many menu items were saved
            $menuItemsCount = 0;
            foreach ($cleanedMenu as $day => $meals) {
                foreach ($meals as $meal => $description) {
                    if (!empty($description)) {
                        $menuItemsCount++;
                    }
                }
            }
            
            $message = "Menu updated successfully! {$menuItemsCount} menu items have been saved.";
            if ($mealsUpdated > 0) {
                $message .= " {$mealsUpdated} existing meals have been updated with the new menu.";
            }
            
            // Return redirect with flash message for better user experience
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect' => route('warden.manage-hostel.show', $hostel)
                ]);
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            \Log::error('Menu update error: ' . $e->getMessage());
            \Log::error('Menu update error trace: ' . $e->getTraceAsString());
            \Log::error('Menu update error file: ' . $e->getFile() . ':' . $e->getLine());
            $errorMessage = 'An error occurred while updating the menu. Please try again.';
            
            // If in debug mode, show more detailed error
            if ($request->has('debug')) {
                $errorMessage .= ' Error: ' . $e->getMessage();
            }
            
            // Return redirect with flash message for better user experience
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ]);
            }
            
            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Update meal menu for a hostel.
     */
    public function updateMealMenu(Request $request, $id)
    {
        try {
            $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($id);
            $mealMenu = $request->input('meal_menu', []);
            
            // Debug logging
            \Log::info('Meal menu update request received', [
                'hostel_id' => $id,
                'warden_id' => Auth::id(),
                'meal_menu_data' => $mealMenu,
                'all_data' => $request->all(),
                'method' => $request->method(),
                'url' => $request->url(),
                'is_ajax' => $request->ajax()
            ]);
            
            // Validate that meal menu data is provided
            if (empty($mealMenu)) {
                $errorMessage = 'No meal menu data provided. Please fill in at least one meal item.';
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ]);
                }
                return redirect()->back()->with('error', $errorMessage);
            }
            
            // Clean up and validate meal menu data for weekly format
            $cleanedMealMenu = [];
            $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
            $mealTypes = ['breakfast', 'lunch', 'snacks', 'dinner'];
            
            foreach ($days as $day) {
                $cleanedMealMenu[$day] = [];
                foreach ($mealTypes as $mealType) {
                    if (isset($mealMenu[$day][$mealType])) {
                        $menuText = trim($mealMenu[$day][$mealType] ?? '');
                        $cleanedMealMenu[$day][$mealType] = $menuText;
                    } else {
                        $cleanedMealMenu[$day][$mealType] = '';
                    }
                }
            }
            
            // Check if at least one meal item is filled
            $hasContent = false;
            foreach ($cleanedMealMenu as $day => $meals) {
                foreach ($meals as $mealType => $menuText) {
                    if (!empty($menuText)) {
                        $hasContent = true;
                        break 2;
                    }
                }
            }
            
            if (!$hasContent) {
                $errorMessage = 'Please fill in at least one meal item before updating.';
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ]);
                }
                return redirect()->back()->with('error', $errorMessage);
            }
            
            // Save the meal menu to the hostel (using the weekly format)
            $hostel->meal_menu = $cleanedMealMenu;
            $hostel->save();
            
            // Update existing meals in the database to reflect the new menu
            $mealsUpdated = 0;
            $existingMeals = \App\Models\Meal::where('hostel_id', $hostel->id)
                ->where('meal_date', '>=', \Carbon\Carbon::today())
                ->get();
            
            foreach ($existingMeals as $meal) {
                // Get the day name based on the meal date
                $dayIndex = $meal->meal_date->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.
                $dayName = strtolower($days[$dayIndex]);
                $newMenuDesc = $cleanedMealMenu[$dayName][$meal->meal_type] ?? '';
                
                // Only update if the menu description has changed
                if ($newMenuDesc !== $meal->menu_description) {
                    $meal->menu_description = $newMenuDesc;
                    $meal->save();
                    $mealsUpdated++;
                }
            }
            
            // Count how many meal items were saved
            $mealItemsCount = 0;
            foreach ($cleanedMealMenu as $day => $meals) {
                foreach ($meals as $mealType => $menuText) {
                    if (!empty($menuText)) {
                        $mealItemsCount++;
                    }
                }
            }
            
            $message = "Meal menu updated successfully! {$mealItemsCount} meal items have been saved.";
            if ($mealsUpdated > 0) {
                $message .= " {$mealsUpdated} existing meals have been updated with the new menu.";
            }
            
            // Return JSON response for AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            \Log::error('Meal menu update error: ' . $e->getMessage());
            \Log::error('Meal menu update error trace: ' . $e->getTraceAsString());
            
            $errorMessage = 'An error occurred while updating the meal menu. Please try again.';
            
            // Return JSON response for AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ]);
            }
            
            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Update facilities description for a hostel.
     */
    public function updateFacilities(Request $request, $id)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($id);
        $hostel->description = $request->input('description', '');
        $hostel->save();
        return redirect()->back()->with('success', 'Facilities description updated.');
    }

    public function students($id)
    {
        // Use the selected hostel ID from request if available, otherwise use the original ID
        $selectedHostelId = request('hostel_id', $id);
        $hostel = Hostel::where('warden_id', Auth::id())->with(['roomApplications.student', 'roomApplications.roomType', 'roomTypes', 'rooms'])->findOrFail($selectedHostelId);
        $perPage = request('per_page', 10);
        $applicationsQuery = $hostel->roomApplications()
            ->whereIn('status', ['pending', 'approved'])
            ->with(['student', 'roomType', 'student.roomAssignments.room']);

        // Filtering logic
        if (request()->filled('name')) {
            $name = request('name');
            $applicationsQuery->whereHas('student', function($q) use ($name) {
                $q->where('name', 'like', "%$name%");
            });
        }
        if (request()->filled('room_type')) {
            $roomType = request('room_type');
            $applicationsQuery->whereHas('roomType', function($q) use ($roomType) {
                $q->where('type', $roomType);
            });
        }
        if (request()->filled('email')) {
            $email = request('email');
            $applicationsQuery->whereHas('student', function($q) use ($email) {
                $q->where('email', 'like', "%$email%");
            });
        }
        if (request()->filled('room_no')) {
            $roomNo = request('room_no');
            if ($roomNo === 'none') {
                // Students with no room assignment in this hostel
                $applicationsQuery->whereDoesntHave('student.roomAssignments', function($q) use ($hostel) {
                    $q->where('room_id', '!=', null)
                      ->whereHas('room', function($qr) use ($hostel) {
                          $qr->where('hostel_id', $hostel->id);
                      });
                });
            } else {
                $applicationsQuery->whereHas('student.roomAssignments.room', function($q) use ($roomNo, $hostel) {
                    $q->where('hostel_id', $hostel->id)
                      ->where('room_number', $roomNo);
                });
            }
        }
        if (request()->filled('category')) {
            $category = request('category');
            $applicationsQuery->whereHas('student', function($q) use ($category) {
                $q->where('caste_category', $category);
            });
        }

        $applications = $applicationsQuery->paginate($perPage)->appends(request()->except('page'));
        $allHostels = Hostel::where('warden_id', Auth::id())->with('roomTypes')->get();
        
        $pageTitle = 'Registered Students';
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => url('/warden/dashboard')],
            
            ['name' => 'Registered Students', 'url' => '']
        ];
        
        return view('warden.hostels_students', compact('hostel', 'applications', 'allHostels', 'pageTitle', 'breadcrumbs'));
    }

    public function attendance($id, Request $request)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($id);
        $date = $request->input('date', \Carbon\Carbon::today()->toDateString());

        // Start with all students assigned to this hostel
        $studentsQuery = $hostel->students()->with(['roomAssignments.room.roomType']);

        // Filters
        if ($request->filled('name')) {
            $studentsQuery->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('room_type')) {
            $studentsQuery->whereHas('roomAssignments', function($q) use ($request) {
                $q->where('status', 'active')
                  ->whereHas('room.roomType', function($qr) use ($request) {
                      $qr->where('type', $request->room_type);
                  });
            });
        }
        if ($request->filled('email')) {
            $studentsQuery->where('email', 'like', '%' . $request->email . '%');
        }
        if ($request->filled('room_no')) {
            $studentsQuery->whereHas('roomAssignments', function($q) use ($request) {
                $q->where('status', 'active')
                  ->whereHas('room', function($qr) use ($request) {
                      $qr->where('room_number', $request->room_no);
                  });
            });
        }
        // Fix: join students table for category filter
        if ($request->filled('category')) {
            $studentsQuery->join('students', 'users.email', '=', 'students.email')
                ->where('students.caste_category', $request->category)
                ->select('users.*');
        }

        $students = $studentsQuery->get();

        // Date range logic
        $dateRange = $request->input('date_range');
        $dates = [];
        $startDate = $endDate = null;
        if ($dateRange && strpos($dateRange, ' - ') !== false) {
            [$startDate, $endDate] = array_map('trim', explode(' - ', $dateRange));
            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
            foreach ($period as $dt) {
                $dates[] = $dt->format('Y-m-d');
            }
        } else {
            $dates[] = $date;
            $startDate = $endDate = $date;
        }

        // Attendance records for all students in the date range
        $records = \App\Models\HostelAttendance::whereIn('student_id', $students->pluck('id'))
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy(['student_id', 'date']);

        $attendanceExists = $records->count() > 0;
        
        $pageTitle = 'Hostel Attendance';
        $breadcrumbs = [
            ['name' => 'Dasboard', 'url' => url('/warden/dashboard')],
            
            ['name' => 'Hostel Attendance', 'url' => '']
        ];
        
        return view('warden.hostels_attendance', compact('hostel', 'students', 'records', 'date', 'attendanceExists', 'dates', 'startDate', 'endDate', 'pageTitle', 'breadcrumbs'));
    }

    public function markAttendance(Request $request, $id)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($id);
        $date = $request->input('date', Carbon::today()->toDateString());
        $students = $hostel->students()->paginate(15); // 15 per page
        $records = \App\Models\HostelAttendance::whereIn('student_id', $students->pluck('id'))
            ->where('date', $date)
            ->get();
        $editMode = $request->has('edit') && $request->input('edit');
        return view('warden.hostels_mark_attendance', compact('hostel', 'students', 'date', 'records', 'editMode'));
    }

    public function storeAttendance(Request $request, $id)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($id);
        $validated = $request->validate([
            'date' => 'required|date',
            'status' => 'array',
            'status.*' => 'in:Taken,Skipped,On Leave,Holiday',
        ]);
        $wardenId = Auth::id();
        $remarks = $request->input('remarks', []);

        $editMode = $request->has('edit') && $request->input('edit');

        // Prevent duplicate attendance for the same date, unless in edit mode
        if (!$editMode) {
            $attendanceExists = \App\Models\HostelAttendance::where('hostel_id', $hostel->id)
                ->where('date', $validated['date'])
                ->exists();
            if ($attendanceExists) {
                return redirect()->back()->with('error', 'Attendance already taken for this date.');
            }
        }

        foreach ($validated['status'] as $studentId => $state) {
            \App\Models\HostelAttendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'hostel_id' => $hostel->id,
                    'date' => $validated['date'],
                ],
                [
                    'status' => $state,
                    'marked_by' => $wardenId,
                    'remarks' => $remarks[$studentId] ?? null,
                ]
            );
            if ($state === 'Skipped') {
                $student = \App\Models\User::find($studentId);
                $parentEmail = $student->parent_email;
                if ($parentEmail) {
                    Notification::route('mail', $parentEmail)
                        ->notify(new StudentAbsentNotification($student, $validated['date']));
                }
            }
        }
        if ($editMode) {
            return redirect()->route('warden.hostels.attendance', [$hostel->id, 'date' => $validated['date']])
                ->with('success', 'Hostel attendance updated!');
        }
        return redirect()->back()->with('success', 'Hostel attendance saved!');
    }

    /**
     * AJAX: Return the edit attendance form HTML for inline display
     */
    public function ajaxEditAttendanceForm(Request $request, $id)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($id);
        $date = $request->input('date', \Carbon\Carbon::today()->toDateString());
        $students = $hostel->students;
        $records = HostelAttendance::whereIn('student_id', $students->pluck('id'))
            ->whereDate('date', $date)
            ->get();
        return view('warden.partials.hostels_edit_attendance_form', compact('hostel', 'students', 'date', 'records'))->render();
    }

    /**
     * Show hostel selection for management
     */
    public function manageIndex()
    {
        $hostels = Hostel::where('warden_id', Auth::id())->get();
        
        $pageTitle = 'Manage Hostel';
        $breadcrumbs = [
            ['name' => 'Dasboard', 'url' => url('/warden/dashboard')],
            ['name' => 'Manage Hostel', 'url' => '']
        ];
        
        return view('warden.manage_hostel.index', compact('hostels', 'pageTitle', 'breadcrumbs'));
    }

    /**
     * Show hostel management panel
     */
    public function manage($id, Request $request)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->with(['roomTypes', 'rooms'])->findOrFail($id);
        // Check if we have a pending room type addition in session
        $pendingRoomType = session('pending_room_type');
        $pendingRoomCount = session('pending_room_count');
        $pendingRoomTypeId = session('pending_room_type_id');
        $pendingAddRooms = session('pending_add_rooms');
        
        $pageTitle = 'Manage Hostel';
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => url('/warden/dashboard')],
            ['name' => 'Manage Hostel', 'url' => route('warden.manage-hostel.index')],
            ['name' => $hostel->name, 'url' => '']
        ];
        
        return view('warden.manage_hostel.show', compact('hostel', 'pendingRoomType', 'pendingRoomCount', 'pendingRoomTypeId', 'pendingAddRooms', 'pageTitle', 'breadcrumbs'));
    }

    /**
     * Store room type and number of rooms, then show room number/floor form
     */
    public function storeRoomType(Request $request, $id)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($id);
        $validated = $request->validate([
            'type' => 'required|string|max:50',
            'capacity' => 'required|integer|min:1|max:20',
            'number_of_rooms' => 'required|integer|min:1|max:100',
            'price_per_month' => 'required|numeric|min:0',
        ]);
        // Create the room type
        $roomType = $hostel->roomTypes()->create([
            'type' => $validated['type'],
            'capacity' => $validated['capacity'],
            'price_per_month' => $validated['price_per_month'],
            'total_rooms' => $validated['number_of_rooms'],
        ]);
        // Store info in session for next step
        return redirect()->route('warden.manage-hostel.show', $hostel->id)
            ->with('pending_room_type', $roomType)
            ->with('pending_room_count', $validated['number_of_rooms'])
            ->with('pending_room_type_id', $roomType->id);
    }

    /**
     * Store room type and number of rooms, then show room number/floor form
     */
    public function addRooms(Request $request, $id)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($id);
        $validated = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'number_of_rooms' => 'required|integer|min:1|max:10',
        ]);
        
        $roomType = $hostel->roomTypes()->find($validated['room_type_id']);
        
        // Store info in session for next step
        return redirect()->route('warden.manage-hostel.show', $hostel->id)
            ->with('pending_add_rooms', [
                'room_type_id' => $validated['room_type_id'],
                'room_type_name' => $roomType->type,
                'capacity' => $roomType->capacity,
                'price_per_month' => $roomType->price_per_month,
                'number_of_rooms' => $validated['number_of_rooms'],
            ]);
    }

    /**
     * Store rooms for a room type (after assigning numbers/floors)
     */
    public function storeRooms(Request $request, $id)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($id);
        $validated = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'rooms' => 'required|array',
            'rooms.*.room_number' => 'required|string|max:50',
            'rooms.*.floor_number' => 'required|string|max:10',
        ]);
        $roomType = $hostel->roomTypes()->find($validated['room_type_id']);
        foreach ($validated['rooms'] as $roomData) {
            $hostel->rooms()->create([
                'room_type_id' => $validated['room_type_id'],
                'room_number' => $roomData['room_number'],
                'floor' => $roomData['floor_number'],
                'status' => 'available',
                'current_occupants' => 0,
                'max_occupants' => $roomType->capacity,
            ]);
        }
        return redirect()->back()->with('success', 'Rooms added successfully.');
    }

    /**
     * Store a single room
     */
    public function storeSingleRoom(Request $request, $id)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($id);
        $validated = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'room_number' => 'required|string|max:50',
            'floor' => 'required|string|max:10',
        ]);
        
        $roomType = $hostel->roomTypes()->find($validated['room_type_id']);
        
        // Check for duplicate room number within the same hostel and floor
        $existingRoom = $hostel->rooms()
            ->where('room_number', $validated['room_number'])
            ->where('floor', $validated['floor'])
            ->first();
            
        if ($existingRoom) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['room_number' => "Room {$validated['room_number']} on floor {$validated['floor']} already exists."]);
        }
        
        $hostel->rooms()->create([
            'room_type_id' => $validated['room_type_id'],
            'room_number' => $validated['room_number'],
            'floor' => $validated['floor'],
            'status' => 'available',
            'current_occupants' => 0,
            'max_occupants' => $roomType->capacity,
        ]);
        
        return redirect()->back()->with('success', 'Room added successfully.');
    }

    /**
     * Bulk delete rooms
     */
    public function bulkDeleteRooms(Request $request, $hostelId)
    {
        \Log::info('Bulk delete rooms called', [
            'hostel_id' => $hostelId,
            'request_data' => $request->all()
        ]);
        
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($hostelId);
        
        // Check if this is the final confirmation step
        if (request()->has('final_confirmation') && request('final_confirmation') === 'true') {
            $validated = $request->validate([
                'room_ids' => 'required|array',
                'room_ids.*' => 'required|integer|exists:rooms,id'
            ]);
            
            \Log::info('Validation passed', ['validated' => $validated]);
            
            $deletedCount = 0;
            $roomsWithOccupants = [];
            
            foreach ($validated['room_ids'] as $roomId) {
                $room = $hostel->rooms()->find($roomId);
                
                if ($room) {
                    // Check if room has occupants
                    $occupants = $room->roomAssignments()->where('status', 'active')->count();
                    
                    \Log::info('Processing room', [
                        'room_id' => $roomId,
                        'room_number' => $room->room_number,
                        'occupants' => $occupants
                    ]);
                    
                    if ($occupants > 0) {
                        $roomsWithOccupants[] = "Room {$room->room_number} (has {$occupants} occupant(s))";
                    } else {
                        $room->delete();
                        $deletedCount++;
                        \Log::info('Room deleted', ['room_id' => $roomId]);
                    }
                }
            }
            
            $message = "Successfully deleted {$deletedCount} room(s).";
            
            if (!empty($roomsWithOccupants)) {
                $message .= " Could not delete the following rooms as they have occupants: " . implode(', ', $roomsWithOccupants);
            }
            
            \Log::info('Bulk delete completed', [
                'deleted_count' => $deletedCount,
                'rooms_with_occupants' => $roomsWithOccupants,
                'message' => $message
            ]);
            
            return redirect()->back()->with('success', $message);
        }
        
        // Check if this is the third confirmation step
        if (request()->has('third_confirmation') && request('third_confirmation') === 'true') {
            $validated = $request->validate([
                'room_ids' => 'required|array',
                'room_ids.*' => 'required|integer|exists:rooms,id'
            ]);
            
            $rooms = $hostel->rooms()->whereIn('id', $validated['room_ids'])->with('roomType')->get();
            $step = 3;
            return view('warden.rooms.confirm_bulk_delete', compact('hostel', 'rooms', 'step'));
        }
        
        // Check if this is the second confirmation step
        if (request()->has('second_confirmation') && request('second_confirmation') === 'true') {
            $validated = $request->validate([
                'room_ids' => 'required|array',
                'room_ids.*' => 'required|integer|exists:rooms,id'
            ]);
            
            $rooms = $hostel->rooms()->whereIn('id', $validated['room_ids'])->with('roomType')->get();
            $step = 2;
            return view('warden.rooms.confirm_bulk_delete', compact('hostel', 'rooms', 'step'));
        }
        
        // First confirmation step
        $validated = $request->validate([
            'room_ids' => 'required|array',
            'room_ids.*' => 'required|integer|exists:rooms,id'
        ]);
        
        $rooms = $hostel->rooms()->whereIn('id', $validated['room_ids'])->with('roomType')->get();
        $step = 1;
        return view('warden.rooms.confirm_bulk_delete', compact('hostel', 'rooms', 'step'));
    }

    /**
     * Store rooms with details (for adding rooms to existing room types)
     */
    public function storeRoomsWithDetails(Request $request, $id)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($id);
        $validated = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'rooms' => 'required|array',
            'rooms.*.room_number' => 'required|string|max:50',
            'rooms.*.floor_number' => 'required|string|max:10',
        ]);
        
        $roomType = $hostel->roomTypes()->find($validated['room_type_id']);
        
        // Check for duplicate room numbers within the same hostel and floor
        $existingRooms = $hostel->rooms()->whereIn('floor', collect($validated['rooms'])->pluck('floor_number'))->get();
        $duplicates = [];
        
        foreach ($validated['rooms'] as $roomData) {
            $existingRoom = $existingRooms->where('room_number', $roomData['room_number'])
                                         ->where('floor', $roomData['floor_number'])
                                         ->first();
            if ($existingRoom) {
                $duplicates[] = "Room {$roomData['room_number']} on floor {$roomData['floor_number']}";
            }
        }
        
        if (!empty($duplicates)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['rooms' => 'The following rooms already exist: ' . implode(', ', $duplicates)]);
        }
        
        foreach ($validated['rooms'] as $roomData) {
            $hostel->rooms()->create([
                'room_type_id' => $validated['room_type_id'],
                'room_number' => $roomData['room_number'],
                'floor' => $roomData['floor_number'],
                'status' => 'available',
                'current_occupants' => 0,
                'max_occupants' => $roomType->capacity,
            ]);
        }
        
        return redirect()->route('warden.manage-hostel.show', $hostel->id)
            ->with('success', 'Rooms added successfully.');
    }

    /**
     * Update rent for room types
     */
    public function updateRent(Request $request, $id)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($id);
        
        $validated = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'price_per_month' => 'required|numeric|min:0',
        ]);

        $hostel->roomTypes()->where('id', $validated['room_type_id'])->update([
            'price_per_month' => $validated['price_per_month']
        ]);
        
        return redirect()->back()->with('success', 'Rent updated successfully.');
    }

    public function attendanceHostels()
    {
        $hostels = Hostel::where('warden_id', Auth::id())->get();
        return view('warden.hostels_attendance_hostels', compact('hostels'));
    }

    public function downloadAttendanceCsv(Request $request, $hostelId)
    {
        $date = $request->input('date', now()->toDateString());
        $hostel = \App\Models\Hostel::findOrFail($hostelId);
        $students = $hostel->students;
        $attendanceRecords = HostelAttendance::whereIn('student_id', $students->pluck('id'))
            ->where('date', $date)
            ->get()
            ->groupBy('student_id');
        $filename = 'hostel-attendance-' . $date . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $columns = ['Student Name', 'Room No', 'Status', 'Remarks'];
        $callback = function() use ($students, $attendanceRecords, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($students as $student) {
                $record = $attendanceRecords[$student->id][0] ?? null;
                $status = $record ? ($record->status === 'Taken' ? 'P' : ($record->status === 'Skipped' ? 'A' : ($record->status === 'On Leave' ? 'L' : ($record->status === 'Holiday' ? 'H' : '')))) : '';
                $remarks = $record->remarks ?? '';
                $row = [
                    $student->name,
                    optional($student->roomAssignments->first()->room)->room_number ?? '-',
                    $status,
                    $remarks,
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function downloadAttendanceSummaryExcel(Request $request, $hostelId)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $hostel = \App\Models\Hostel::findOrFail($hostelId);
        $students = $hostel->students;
        $studentIds = $students->pluck('id');
        $attendanceRecords = HostelAttendance::whereIn('student_id', $studentIds)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();
        $totalStudents = $students->count();
        $present = 0; $absent = 0; $onLeave = 0; $holiday = 0;
        $studentPresentCount = [];
        $studentAbsentCount = [];
        foreach ($students as $student) {
            $records = $attendanceRecords->where('student_id', $student->id);
            $presentCount = $records->where('status', 'Taken')->count();
            $absentCount = $records->where('status', 'Skipped')->count();
            $leaveCount = $records->where('status', 'On Leave')->count();
            $holidayCount = $records->where('status', 'Holiday')->count();
            $present += $presentCount;
            $absent += $absentCount;
            $onLeave += $leaveCount;
            $holiday += $holidayCount;
            $studentPresentCount[$student->id] = $presentCount;
            $studentAbsentCount[$student->id] = $absentCount;
        }
        $totalRecords = $present + $absent + $onLeave + $holiday;
        $attendancePercent = $totalRecords > 0 ? round(($present / $totalRecords) * 100) : 0;
        $mostPresentId = collect($studentPresentCount)->sortDesc()->keys()->first();
        $mostAbsentId = collect($studentAbsentCount)->sortDesc()->keys()->first();
        $mostPresentStudent = $mostPresentId ? $students->find($mostPresentId) : null;
        $mostAbsentStudent = $mostAbsentId ? $students->find($mostAbsentId) : null;
        $mostPresentPercent = $mostPresentId && $totalRecords > 0 ? round(($studentPresentCount[$mostPresentId] / $totalRecords) * 100) : 0;
        $mostAbsentPercent = $mostAbsentId && $totalRecords > 0 ? round(($studentAbsentCount[$mostAbsentId] / $totalRecords) * 100) : 0;
        $generatedBy = auth()->user()->name ?? 'Hostel System';
        $generatedOn = now()->format('d-m-Y h:i A');
        $summary = [
            ['Metric', 'Value'],
            ['Total Students', $totalStudents],
            ['Total Present', $present],
            ['Total Absent', $absent],
            ['On Leave', $onLeave],
            ['Holiday', $holiday],
            ['Attendance Percentage', $attendancePercent . '%'],
            ['Most Present Student', $mostPresentStudent ? $mostPresentStudent->name . ' (' . $mostPresentPercent . '%)' : '-'],
            ['Most Absent Student', $mostAbsentStudent ? $mostAbsentStudent->name . ' (' . $mostAbsentPercent . '%)' : '-'],
            ['Generated By', $generatedBy],
            ['Generated On', $generatedOn],
        ];
        $pdf = \PDF::loadView('warden.pdf.attendance_summary', [
            'hostel' => $hostel,
            'summary' => $summary,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
        $filename = 'attendance-summary-' . $hostel->name . '-' . $startDate . '-to-' . $endDate . '.pdf';
        return $pdf->download($filename);
    }

    // Add a single student manually
    public function addStudent(Request $request, $hostelId)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($request->input('hostel_id'));
        $roomType = $hostel->roomTypes()->findOrFail($request->input('room_type_id'));
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'usn' => 'required|string|max:255|unique:users,usn',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'hostel_id' => 'required|exists:hostels,id',
            'room_type_id' => 'required|exists:room_types,id',
        ]);
        $student = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'usn' => $validated['usn'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'role' => 'student',
            'password' => bcrypt('password'), // Default password, should be changed by student
        ]);
        RoomApplication::create([
            'student_id' => $student->id,
            'hostel_id' => $hostel->id,
            'status' => 'pending',
            'application_date' => now(),
            'room_type_id' => $roomType->id,
            'amount' => 0,
        ]);
        return redirect()->back()->with('success', 'Student added successfully.');
    }

    // Bulk upload students via CSV
    public function bulkUploadStudents(Request $request, $hostelId)
    {
        $wardenId = Auth::id();
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);
        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);
        $count = 0;
        $attempted = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $attempted++;
            $data = array_combine($header, $row);
            if (!isset($data['name'], $data['email'], $data['usn'], $data['hostel'], $data['room_type'])) continue;
            if (User::where('email', $data['email'])->exists()) continue;
            if (User::where('usn', $data['usn'])->exists()) continue;
            $hostel = Hostel::where('warden_id', $wardenId)->where('name', $data['hostel'])->first();
            if (!$hostel) continue;
            $roomType = $hostel->roomTypes()->where('type', $data['room_type'])->first();
            if (!$roomType) continue;
            $student = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'usn' => $data['usn'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'role' => 'student',
                'password' => bcrypt('password'),
            ]);
            RoomApplication::create([
                'student_id' => $student->id,
                'hostel_id' => $hostel->id,
                'status' => 'pending',
                'application_date' => now(),
                'room_type_id' => $roomType->id,
                'amount' => 0,
            ]);
            $count++;
        }
        fclose($handle);
        return redirect()->back()->with('success', "$count students added successfully from CSV. $attempted rows processed.");
    }

    // Bulk delete students from a hostel
    public function deleteStudents(Request $request, $hostelId)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($hostelId);
        $studentIds = $request->input('student_ids', []);
        
        if (empty($studentIds)) {
            return redirect()->back()->with('error', 'No students selected for deletion.');
        }

        // Check if this is the confirmation step
        if ($request->input('confirmed') === 'true') {
            // Delete RoomApplications and Users
            \App\Models\RoomApplication::where('hostel_id', $hostel->id)->whereIn('student_id', $studentIds)->delete();
            \App\Models\User::whereIn('id', $studentIds)->delete();
            return redirect()->back()->with('success', 'Selected students deleted successfully.');
        } else {
            // First step - show confirmation
            $students = \App\Models\User::whereIn('id', $studentIds)->get();
            
            if ($students->isEmpty()) {
                return redirect()->back()->with('error', 'No valid students found for deletion.');
            }
            
            return view('warden.hostels.students.delete-confirmation', compact('students', 'studentIds', 'hostel'));
        }
    }

    public function updateFees(Request $request, $id)
    {
        $hostel = \App\Models\Hostel::where('warden_id', \Auth::id())->findOrFail($id);
        
        // Collect all fees from the form
        $fees = [];
        $createdFees = 0;
        $existingFees = 0;
        
        // Default fees
        if ($request->has('fees')) {
            foreach ($request->input('fees') as $type => $amount) {
                if ($amount !== null && $amount !== '' && is_numeric($amount)) {
                    $fees[] = [
                        'type' => $type,
                        'amount' => floatval($amount),
                    ];
                }
            }
        }
        // Optional fees
        if ($request->has('optional_fees')) {
            foreach ($request->input('optional_fees') as $fee) {
                if (!empty($fee['type']) && isset($fee['amount']) && $fee['amount'] !== '' && is_numeric($fee['amount'])) {
                    $fees[] = [
                        'type' => $fee['type'],
                        'amount' => floatval($fee['amount']),
                    ];
                }
            }
        }
        
        // Update hostel fees
        $hostel->fees = $fees;
        $hostel->save();
        
        // Get all students currently in this hostel (with active room assignments)
        $students = \App\Models\User::whereHas('roomAssignments', function($q) use ($hostel) {
            $q->where('status', 'active')
              ->whereHas('room', function($qr) use ($hostel) {
                  $qr->where('hostel_id', $hostel->id);
              });
        })->get();
        
        // Create pending fee records for all students
        foreach ($students as $student) {
            foreach ($fees as $fee) {
                // Check if this fee type already exists for this student
                $existingFee = \App\Models\StudentFee::where('student_id', $student->id)
                    ->where('hostel_id', $hostel->id)
                    ->where('fee_type', $fee['type'])
                    ->first();
                
                // Only create if it doesn't exist
                if (!$existingFee) {
                    try {
                        \App\Models\StudentFee::create([
                            'student_id' => $student->id,
                            'hostel_id' => $hostel->id,
                            'fee_type' => $fee['type'],
                            'amount' => $fee['amount'],
                            'status' => 'pending',
                        ]);
                        $createdFees++;
                    } catch (\Exception $e) {
                        \Log::error("Failed to create fee for student {$student->id}: " . $e->getMessage());
                    }
                } else {
                    $existingFees++;
                }
            }
        }
        
        $studentCount = $students->count();
        $feeCount = count($fees);
        
        $message = "Fees updated successfully. ";
        if ($createdFees > 0) {
            $message .= "Created {$createdFees} new pending fee(s) for {$studentCount} student(s). ";
        }
        if ($existingFees > 0) {
            $message .= "{$existingFees} fee(s) already existed. ";
        }
        if ($createdFees === 0 && $existingFees === 0) {
            $message .= "No new fees were created (all fees already exist).";
        }
        
        return redirect()->back()->with('success', $message);
    }

    /**
     * Get all deleted hostels for the current warden
     */
    public function deleted()
    {
        $deletedHostels = Hostel::onlyTrashed()
            ->where('warden_id', Auth::id())
            ->get()
            ->map(function ($hostel) {
                return [
                    'id' => $hostel->id,
                    'name' => $hostel->name,
                    'type' => ucfirst($hostel->type),
                    'deleted_at' => $hostel->deleted_at->format('M d, Y H:i')
                ];
            });

        return response()->json([
            'hostels' => $deletedHostels
        ]);
    }

    /**
     * Restore a deleted hostel
     */
    public function restore($id)
    {
        $hostel = Hostel::onlyTrashed()
            ->where('warden_id', Auth::id())
            ->findOrFail($id);

        $hostel->restore();

        return response()->json([
            'success' => true,
            'message' => 'Hostel restored successfully'
        ]);
    }
}
