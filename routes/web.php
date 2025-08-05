<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Warden\DashboardController;
use App\Http\Controllers\Warden\HostelController;
use App\Http\Controllers\Warden\RoomController;
use App\Http\Controllers\Warden\RoomTypeController;
use App\Http\Controllers\Warden\ApplicationController;
use App\Http\Controllers\Warden\MealController;
use App\Http\Controllers\Warden\AttendanceController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\HostelController as StudentHostelController;
use App\Http\Controllers\Student\ApplicationController as StudentApplicationController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/search', [\App\Http\Controllers\GlobalSearchController::class, 'index'])->name('global.search');

// Warden Student Management
Route::middleware(['auth', 'warden'])->prefix('warden')->name('warden.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('hostels', HostelController::class);
    
    // Deleted hostels routes
    Route::get('/hostels/deleted', [HostelController::class, 'deleted'])->name('hostels.deleted');
    Route::post('/hostels/{hostel}/restore', [HostelController::class, 'restore'])->name('hostels.restore');
    
    // Enhanced Hostel Management Routes
    Route::get('/manage-hostel', [HostelController::class, 'manageIndex'])->name('manage-hostel.index');
    Route::get('/manage-hostel/{hostel}', [HostelController::class, 'manage'])->name('manage-hostel.show');
    Route::post('/manage-hostel/{hostel}/room-types', [HostelController::class, 'storeRoomType'])->name('manage-hostel.room-types.store');
    Route::post('/manage-hostel/{hostel}/rooms', [HostelController::class, 'storeRooms'])->name('manage-hostel.rooms.store');
    Route::post('/manage-hostel/{hostel}/rooms/single', [HostelController::class, 'storeSingleRoom'])->name('manage-hostel.rooms.single.store');
    Route::post('/hostels/{hostel}/rooms/bulk-delete', [HostelController::class, 'bulkDeleteRooms'])->name('warden.hostels.rooms.bulk-delete');
    Route::delete('/hostels/{hostel}/rooms/{room}', [RoomController::class, 'destroy'])->name('hostels.rooms.destroy');
    Route::post('/manage-hostel/{hostel}/add-rooms', [HostelController::class, 'addRooms'])->name('manage-hostel.add-rooms');
    Route::post('/manage-hostel/{hostel}/store-rooms-details', [HostelController::class, 'storeRoomsWithDetails'])->name('manage-hostel.store-rooms-details');
    Route::post('/manage-hostel/{hostel}/rent', [HostelController::class, 'updateRent'])->name('manage-hostel.rent.update');
    Route::post('/manage-hostel/{hostel}/fees', [HostelController::class, 'updateFees'])->name('manage-hostel.fees.update');
    Route::post('/manage-hostel/{hostel}/menu', [HostelController::class, 'updateMenu'])->name('manage-hostel.menu.update');
    Route::post('/manage-hostel/{hostel}/meal-menu', [HostelController::class, 'updateMealMenu'])->name('manage-hostel.meal-menu.update');
    Route::post('/manage-hostel/{hostel}/facilities', [HostelController::class, 'updateFacilities'])->name('manage-hostel.facilities.update');
    Route::delete('/manage-hostel/{hostel}/room-type/{id}', [App\Http\Controllers\Warden\RoomTypeController::class, 'destroy'])->name('warden.hostels.room-types.destroy');
    
    // Enhanced Rooms Management
    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/{hostel}', [RoomController::class, 'show'])->name('rooms.show');
    Route::delete('/rooms/{hostel}/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');
    Route::put('/rooms/{hostel}/{room}', [RoomController::class, 'update'])->name('rooms.update');
    
    // Room Allotment System
    Route::get('/room-allotment', [ApplicationController::class, 'allotmentIndex'])->name('room-allotment.index');
    Route::get('/room-allotment/{application}', [ApplicationController::class, 'allotmentShow'])->name('room-allotment.show');
    Route::post('/room-allotment/{application}/allot', [ApplicationController::class, 'allotRoom'])->name('room-allotment.allot');
    Route::post('room-allotment/bulk-reject', [ApplicationController::class, 'bulkReject'])->name('room-allotment.bulk_reject');
    
    // Legacy routes (keeping for compatibility)
    Route::post('hostels/{hostel}/update-room-counts', [HostelController::class, 'updateRoomCounts'])->name('hostels.updateRoomCounts');
    Route::post('hostels/{hostel}/update-menu', [HostelController::class, 'updateMenu'])->name('hostels.updateMenu');
    Route::post('hostels/{hostel}/update-facilities', [HostelController::class, 'updateFacilities'])->name('hostels.updateFacilities');
    Route::resource('hostels.room-types', RoomTypeController::class);
    Route::post('hostels/{hostel}/room-types/{roomType}/restore', [RoomTypeController::class, 'restore'])->name('hostels.room-types.restore');
    Route::delete('hostels/{hostel}/room-types/{roomType}/force-delete', [RoomTypeController::class, 'forceDelete'])->name('hostels.room-types.force-delete');
    Route::resource('applications', ApplicationController::class);
    Route::get('/applications/{id}/reject-confirmation', [ApplicationController::class, 'rejectConfirmation'])->name('applications.reject-confirmation');
    Route::resource('meals', MealController::class);
    Route::get('hostels/{hostel}/students', [HostelController::class, 'students'])->name('hostels.students');
    Route::get('hostels/{hostel}/attendance', [HostelController::class, 'attendance'])->name('hostels.attendance');
    Route::get('hostels/{hostel}/attendance/mark', [HostelController::class, 'markAttendance'])->name('hostels.attendance.mark');
    Route::post('warden/hostels/{hostel}/attendance/mark', [HostelController::class, 'storeAttendance'])->name('warden.hostels.attendance.store');
    Route::get('warden/hostels/{hostel}/attendance/edit-form', [App\Http\Controllers\Warden\HostelController::class, 'ajaxEditAttendanceForm'])->name('warden.hostels.attendance.edit-form');

    Route::get('hostels/{hostel}/rooms/bulk-create', [RoomController::class, 'bulkCreate'])->name('hostels.rooms.bulkCreate');
    Route::post('hostels/rooms/bulk-store', [RoomController::class, 'bulkStore'])->name('rooms.bulkStore');
    Route::get('hostels/{hostel}/rooms/create', [RoomController::class, 'create'])->name('hostels.rooms.create');
    Route::post('hostels/{hostel}/rooms', [RoomController::class, 'store'])->name('hostels.rooms.store');
    Route::get('hostels/{hostel}/rooms/{room}/edit', [RoomController::class, 'edit'])->name('hostels.rooms.edit');
    Route::put('hostels/{hostel}/rooms/{room}', [RoomController::class, 'update'])->name('hostels.rooms.update');
    Route::delete('hostels/{hostel}/rooms/{room}', [RoomController::class, 'destroy'])->name('hostels.rooms.destroy');
    Route::post('hostels/{hostel}/students/add', [HostelController::class, 'addStudent'])->name('hostels.students.add');
    Route::post('hostels/{hostel}/students/bulk-upload', [HostelController::class, 'bulkUploadStudents'])->name('hostels.students.bulk_upload');
    Route::delete('hostels/{hostel}/students/delete', [HostelController::class, 'deleteStudents'])->name('hostels.students.delete');
    Route::get('select-hostel/manage', function() {
        $hostels = Auth::user()->managedHostels;
        return view('warden.select_hostel', [
            'hostels' => $hostels,
            'action' => 'manage',
        ]);
    })->name('selectHostel.manage');
    Route::get('select-hostel/rooms', function() {
        $hostels = Auth::user()->managedHostels;
        return view('warden.select_hostel', [
            'hostels' => $hostels,
            'action' => 'rooms',
        ]);
    })->name('selectHostel.rooms');
    Route::get('select-hostel/room-types', function() {
        $hostels = Auth::user()->managedHostels;
        return view('warden.select_hostel', [
            'hostels' => $hostels,
            'action' => 'room-types',
        ]);
    })->name('selectHostel.room-types');
    // Meals Attendance
    Route::get('/meals-attendance', [App\Http\Controllers\Warden\MealsAttendanceController::class, 'index'])->name('warden.meals-attendance.index');
    Route::post('/meals-attendance/fetch-students', [App\Http\Controllers\Warden\MealsAttendanceController::class, 'fetchStudents'])->name('warden.meals-attendance.fetch-students');
    Route::post('/meals-attendance/save', [App\Http\Controllers\Warden\MealsAttendanceController::class, 'saveAttendance'])->name('warden.meals-attendance.save');
    Route::get('/meals-attendance/{hostel}', [App\Http\Controllers\Warden\MealsAttendanceController::class, 'hostel'])->name('warden.warden.meals-attendance.hostel');
    Route::get('/meals-attendance/{hostel?}', [App\Http\Controllers\Warden\MealAttendanceController::class, 'index'])->name('meals-attendance.index');
    Route::post('/meals-attendance/{hostel}', [App\Http\Controllers\Warden\MealAttendanceController::class, 'store'])->name('meals-attendance.store');
    Route::get('/meals-attendance/{hostel}/download-csv', [App\Http\Controllers\Warden\MealAttendanceController::class, 'downloadCsv'])->name('meals-attendance.download-csv');
    Route::get('hostels-attendance', [App\Http\Controllers\Warden\HostelController::class, 'attendanceHostels'])->name('hostels_attendance_hostels');
    Route::get('/hostels/{hostel}/attendance/download-csv', [App\Http\Controllers\Warden\HostelController::class, 'downloadAttendanceCsv'])->name('hostels.attendance.download-csv');
    Route::get('/hostels/{hostel}/attendance/export-summary', [App\Http\Controllers\Warden\HostelController::class, 'downloadAttendanceSummaryExcel'])->name('hostels.attendance.export-summary');
    Route::get('/meals-attendance/{hostel}/export-summary', [App\Http\Controllers\Warden\MealsAttendanceController::class, 'downloadMealsAttendanceSummaryExcel'])->name('meals-attendance.export-summary');
    Route::get('/warden/attendance-report', [AttendanceController::class, 'report'])->name('attendance.report');
    Route::get('/meals-attendance/{hostel}/attendance/download-csv', [App\Http\Controllers\Warden\MealsAttendanceController::class, 'downloadAttendanceCsv'])->name('warden.meals-attendance.download-csv');
    Route::get('/meals-attendance/{hostel}/attendance/download-pdf', [App\Http\Controllers\Warden\MealsAttendanceController::class, 'downloadAttendancePdf'])->name('warden.meals-attendance.download-pdf');
    Route::get('/meals-attendance/{hostel}/attendance/download-csv-full', [App\Http\Controllers\Warden\MealsAttendanceController::class, 'downloadAttendanceCsvFull'])->name('warden.warden.meals-attendance.download-csv-full');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('students/{student}/edit', [\App\Http\Controllers\Warden\StudentController::class, 'edit'])->name('students.edit');
    Route::get('students/{student}', [\App\Http\Controllers\Warden\StudentController::class, 'show'])->name('students.show');
    Route::put('students/{student}', [\App\Http\Controllers\Warden\StudentController::class, 'update'])->name('students.update');
    Route::get('/fees', [App\Http\Controllers\Warden\FeesController::class, 'index'])->name('fees.index');
    Route::get('/fees/create-missing/{hostel}', [App\Http\Controllers\Warden\FeesController::class, 'createMissingFees'])->name('fees.create-missing');
    Route::get('/fees/student-status', [App\Http\Controllers\Warden\FeesController::class, 'studentStatus'])->name('fees.student_status');
    Route::get('/fees/student-status/export/csv', [App\Http\Controllers\Warden\FeesController::class, 'exportCsv'])->name('fees.student_status.export.csv');
    Route::get('/fees/student-status/export/pdf', [App\Http\Controllers\Warden\FeesController::class, 'exportPdf'])->name('fees.student_status.export.pdf');
    Route::get('/fees/student-status/export/word', [App\Http\Controllers\Warden\FeesController::class, 'exportWord'])->name('fees.student_status.export.word');
    Route::post('/warden/fees/notify-parents', [\App\Http\Controllers\Warden\FeesController::class, 'notifyParents'])->name('warden.fees.notify-parents');
    
    // AJAX route for student profile modal
    Route::get('/students/{student}/profile-data', [\App\Http\Controllers\Warden\StudentController::class, 'getProfileData'])->name('students.profile-data');
});

Route::middleware(['auth', 'student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/attendance', [App\Http\Controllers\Student\AttendanceController::class, 'index'])->name('attendance');
    Route::get('/hostels', [StudentHostelController::class, 'index'])->name('hostels.index');
    Route::get('/hostels/{hostel}', [StudentHostelController::class, 'show'])->name('hostels.show');
    Route::get('/hostels/{hostel}/apply', [StudentApplicationController::class, 'create'])->name('applications.create');
    Route::post('/hostels/{hostel}/apply', [StudentApplicationController::class, 'store'])->name('applications.store');
    Route::get('/applications/receipt/{application}', [StudentApplicationController::class, 'receipt'])->name('applications.receipt');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/fees/paid', [App\Http\Controllers\Student\FeesController::class, 'paid'])->name('fees.paid');
    Route::get('/fees/pending', [App\Http\Controllers\Student\FeesController::class, 'pending'])->name('fees.pending');
    Route::post('/fees/pay/{id}', [App\Http\Controllers\Student\FeesController::class, 'pay'])->name('fees.pay');
    Route::get('/fees/receipt/{id}', [App\Http\Controllers\Student\FeesController::class, 'receipt'])->name('fees.receipt');
    Route::get('/fees/receipt/{id}/download', [App\Http\Controllers\Student\FeesController::class, 'downloadReceipt'])->name('fees.receipt.download');
    Route::get('/parents/edit', [App\Http\Controllers\Student\ParentController::class, 'edit'])->name('parents.edit');
    Route::post('/parents/update', [App\Http\Controllers\Student\ParentController::class, 'update'])->name('parents.update');
});

// API: Check if attendance exists for a hostel and date
Route::get('/api/hostels/{hostel}/attendance-exists', function($hostel) {
    $date = request('date');
    $exists = \App\Models\HostelAttendance::where('hostel_id', $hostel)
        ->where('date', $date)
        ->exists();
    return response()->json(['exists' => $exists]);
});

require __DIR__.'/auth.php';
