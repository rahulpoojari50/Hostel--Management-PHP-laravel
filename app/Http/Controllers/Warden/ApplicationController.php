<?php

namespace App\Http\Controllers\Warden;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RoomApplication;
use App\Models\Hostel;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function index()
    {
        $warden = Auth::user();
        $hostelIds = Hostel::where('warden_id', $warden->id)->pluck('id');
        $applications = RoomApplication::with(['student', 'hostel', 'roomType', 'processedBy'])
            ->whereIn('hostel_id', $hostelIds)
            ->latest()
            ->get();
            
        $pageTitle = 'Room Applications';
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => url('/warden/dashboard')],
            ['name' => 'Room Applications', 'url' => '']
        ];
        
        return view('warden.applications.index', compact('applications', 'pageTitle', 'breadcrumbs'));
    }

    public function show($id)
    {
        $warden = Auth::user();
        $hostelIds = Hostel::where('warden_id', $warden->id)->pluck('id');
        $application = RoomApplication::with(['student', 'hostel', 'roomType', 'processedBy'])
            ->whereIn('hostel_id', $hostelIds)
            ->findOrFail($id);
        $availableRooms = [];
        if ($application->status === 'pending' || $application->status === 'rejected') {
            $availableRooms = \App\Models\Room::where('hostel_id', $application->hostel_id)
                ->where('room_type_id', $application->room_type_id)
                ->where('status', 'available')
                ->whereColumn('current_occupants', '<', 'max_occupants')
                ->orderBy('room_number')
                ->get();
        }
        $pageTitle = 'Application Details';
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => url('/warden/dashboard')],
            ['name' => 'Room Applications', 'url' => route('warden.applications.index')],
            ['name' => 'Application Details', 'url' => '']
        ];
        
        return view('warden.applications.show', compact('application', 'availableRooms', 'pageTitle', 'breadcrumbs'));
    }

    public function update(Request $request, $id)
    {
        $warden = Auth::user();
        $hostelIds = Hostel::where('warden_id', $warden->id)->pluck('id');
        $application = RoomApplication::whereIn('hostel_id', $hostelIds)->findOrFail($id);
        $action = $request->input('action');
        $remarks = $request->input('warden_remarks');

        if ($action === 'approve') {
            // Check if application is pending or rejected (for reapproval)
            if ($application->status !== 'pending' && $application->status !== 'rejected') {
                return redirect()->back()->with('error', 'Application already processed.');
            }

            // Check if room_id is provided
            if (!$request->has('room_id') || empty($request->input('room_id'))) {
                // Redirect to room allotment page for room selection
                return redirect()->route('warden.room-allotment.show', $application->id)
                    ->with('info', 'Please select a room to complete the approval process.');
            }
            
            $request->validate([
                'room_id' => ['required', 'exists:rooms,id'],
            ], [
                'room_id.required' => 'Please select a room to proceed with the approval.',
                'room_id.exists' => 'The selected room is not valid.',
            ]);
            $room = \App\Models\Room::findOrFail($request->room_id);
            
            // Check if room is still available
            if ($room->status !== 'available' || $room->current_occupants >= $room->max_occupants) {
                return redirect()->back()->with('error', 'The selected room is no longer available. Please select another room.');
            }
            
            // Check if student already has an active room assignment
            $existingAssignment = \App\Models\RoomAssignment::where('student_id', $application->student_id)
                ->where('status', 'active')
                ->first();
            
            if ($existingAssignment) {
                return redirect()->back()->with('error', 'Student already has an active room assignment.');
            }
            
            // Create the room assignment
            \App\Models\RoomAssignment::create([
                'student_id' => $application->student_id,
                'room_id' => $room->id,
                'assigned_date' => now(),
                'status' => 'active',
            ]);
            // Increment occupants
            $room->increment('current_occupants');
            // If room is now full, mark as occupied
            if ($room->current_occupants >= $room->max_occupants) {
                $room->status = 'occupied';
                $room->save();
            }
            // Store if this was a reapproval
            $wasRejected = $application->status === 'rejected';
            $application->approve($warden, $remarks);
            
            // If this was a reapproval, add a note to the remarks
            if ($wasRejected) {
                $reapprovalNote = "\n\n[REAPPROVED - Originally rejected on " . $application->processed_at->format('Y-m-d H:i:s') . "]";
                $application->update([
                    'warden_remarks' => ($application->warden_remarks ?? '') . $reapprovalNote
                ]);
            }
            
            // Create pending fees for the new student based on hostel's fee structure
            $hostel = $application->hostel;
            if ($hostel->fees && is_array($hostel->fees)) {
                foreach ($hostel->fees as $fee) {
                    \App\Models\StudentFee::create([
                        'student_id' => $application->student_id,
                        'hostel_id' => $hostel->id,
                        'application_id' => $application->id,
                        'fee_type' => $fee['type'],
                        'amount' => $fee['amount'],
                        'status' => 'pending',
                    ]);
                }
            }
            
            $message = $application->status === 'rejected' 
                ? 'Application reapproved and room assigned successfully to ' . $application->student->name . ' in Room ' . $room->room_number . '!'
                : 'Application approved and room assigned successfully to ' . $application->student->name . ' in Room ' . $room->room_number . '!';
            return redirect()->route('warden.hostels.students', $application->hostel_id)->with('success', $message);
        } elseif ($action === 'reject') {
            // Only allow rejection of pending applications
            if ($application->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending applications can be rejected.');
            }
            
            // Check if this is the confirmation step
            if ($request->input('confirmed') === 'true') {
                $application->reject($warden, $remarks);
                return redirect()->route('warden.applications.index')->with('success', 'Application rejected successfully.');
            } else {
                // First step - show confirmation
                return redirect()->route('warden.applications.reject-confirmation', $application->id)
                    ->with('reject_data', [
                        'remarks' => $remarks
                    ]);
            }
        }

        return redirect()->back()->with('error', 'Invalid action.');
    }

    /**
     * Show reject confirmation page
     */
    public function rejectConfirmation($id)
    {
        try {
            $warden = Auth::user();
            $hostelIds = Hostel::where('warden_id', $warden->id)->pluck('id');
            $application = RoomApplication::with(['student', 'hostel', 'roomType'])
                ->whereIn('hostel_id', $hostelIds)
                ->findOrFail($id);
            
            if ($application->status !== 'pending') {
                return redirect()->route('warden.applications.show', $application->id)
                    ->with('error', 'Application already processed.');
            }

            $remarks = session('reject_data.remarks', '');
            $pageTitle = 'Confirm Rejection';
            $breadcrumbs = [
                ['name' => 'Dashboard', 'url' => url('/warden/dashboard')],
                ['name' => 'Room Applications', 'url' => route('warden.applications.index')],
                ['name' => 'Application Details', 'url' => route('warden.applications.show', $application->id)],
                ['name' => 'Confirm Rejection', 'url' => '']
            ];
            
            return view('warden.applications.reject-confirmation', compact('application', 'remarks', 'pageTitle', 'breadcrumbs'));
        } catch (\Exception $e) {
            return redirect()->route('warden.applications.index')
                ->with('error', 'Application not found or you do not have permission to access it.');
        }
    }

    /**
     * Show room allotment index with pending applications
     */
    public function allotmentIndex()
    {
        $pendingApplications = RoomApplication::with(['student', 'hostel', 'roomType'])
            ->where('status', 'pending')
            ->latest()
            ->get(); // No pagination for debugging
        $pageTitle = 'Room Allotment';
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => url('/warden/dashboard')],
            ['name' => 'Room Allotment', 'url' => '']
        ];
        if (request()->ajax()) {
            return view('warden.room_allotment._table', compact('pendingApplications'))->render();
        }
        return view('warden.room_allotment.index', compact('pendingApplications', 'pageTitle', 'breadcrumbs'));
    }

    /**
     * Show room allotment form for specific application
     */
    public function allotmentShow($id)
    {
        $warden = Auth::user();
        $hostelIds = Hostel::where('warden_id', $warden->id)->pluck('id');
        $application = RoomApplication::with(['student', 'hostel', 'roomType'])
            ->whereIn('hostel_id', $hostelIds)
            ->whereIn('status', ['pending', 'rejected'])
            ->findOrFail($id);
        $selectedRoomType = null;
        $roomTypeId = request('room_type_id');
        if ($roomTypeId) {
            $selectedRoomType = $application->hostel->roomTypes->where('id', $roomTypeId)->first();
            $availableRooms = \App\Models\Room::where('hostel_id', $application->hostel_id)
                ->where('room_type_id', $roomTypeId)
                ->where('status', 'available')
                ->whereColumn('current_occupants', '<', 'max_occupants')
                ->with('roomType')
                ->orderBy('room_number')
                ->get();
        } else if (($application->status === 'pending' || $application->status === 'rejected') && $application->room_type_id && $application->roomType && $application->roomType->type) {
            // Default: restrict to requested room type
            $availableRooms = \App\Models\Room::where('hostel_id', $application->hostel_id)
                ->where('room_type_id', $application->room_type_id)
                ->where('status', 'available')
                ->whereColumn('current_occupants', '<', 'max_occupants')
                ->with('roomType')
                ->orderBy('room_number')
                ->get();
        } else {
            // Allow any available room in any hostel managed by this warden
            $availableRooms = \App\Models\Room::whereIn('hostel_id', $hostelIds)
                ->where('status', 'available')
                ->whereColumn('current_occupants', '<', 'max_occupants')
                ->with('roomType')
                ->orderBy('room_number')
                ->get();
        }
        return view('warden.room_allotment.show', compact('application', 'availableRooms', 'selectedRoomType'));
    }

    /**
     * Process room allotment
     */
    public function allotRoom(Request $request, $id)
    {
        $warden = Auth::user();
        $hostelIds = Hostel::where('warden_id', $warden->id)->pluck('id');
        $application = RoomApplication::whereIn('hostel_id', $hostelIds)
            ->whereIn('status', ['pending', 'rejected'])
            ->findOrFail($id);
        
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'warden_remarks' => 'nullable|string|max:500',
        ]);
        
        $room = \App\Models\Room::findOrFail($validated['room_id']);
        
        // Check if student already has an active room assignment
        $existingAssignment = \App\Models\RoomAssignment::where('student_id', $application->student_id)
            ->where('status', 'active')
            ->first();
        
        if ($existingAssignment) {
            return redirect()->back()->with('error', 'Student already has an active room assignment.');
        }
        
        // Verify room is available and matches application requirements
        if (
            $room->hostel_id !== $application->hostel_id ||
            $room->status !== 'available' ||
            $room->current_occupants >= $room->max_occupants
        ) {
            return redirect()->back()->with('error', 'Selected room is not available.');
        }
        
        // Create room assignment
        \App\Models\RoomAssignment::create([
            'student_id' => $application->student_id,
            'room_id' => $room->id,
            'assigned_date' => now(),
            'status' => 'active',
        ]);
        
        // Update room occupancy
        $room->increment('current_occupants');
        if ($room->current_occupants >= $room->max_occupants) {
            $room->status = 'occupied';
            $room->save();
        }
        
        // Approve application
        $application->approve($warden, $validated['warden_remarks'] ?? 'Room allotted successfully.');
        
        // Create pending fees for the new student based on hostel's fee structure
        $hostel = $application->hostel;
        if ($hostel->fees && is_array($hostel->fees)) {
            foreach ($hostel->fees as $fee) {
                \App\Models\StudentFee::create([
                    'student_id' => $application->student_id,
                    'hostel_id' => $hostel->id,
                    'application_id' => $application->id,
                    'fee_type' => $fee['type'],
                    'amount' => $fee['amount'],
                    'status' => 'pending',
                ]);
            }
        }
        
        $message = $application->status === 'rejected' 
            ? 'Application reapproved and room allotted successfully to ' . $application->student->name . ' in Room ' . $room->room_number
            : 'Room allotted successfully to ' . $application->student->name . ' in Room ' . $room->room_number;
        return redirect()->route('warden.hostels.students', $application->hostel_id)
            ->with('success', $message);
    }

    // Bulk reject room applications
    public function bulkReject(Request $request)
    {
        $warden = Auth::user();
        $applicationIds = $request->input('application_ids', []);
        
        if (empty($applicationIds)) {
            return redirect()->back()->with('error', 'No applications selected for rejection.');
        }

        // Check if this is the confirmation step
        if ($request->input('confirmed') === 'true') {
            $applications = RoomApplication::whereIn('id', $applicationIds)->where('status', 'pending')->get();
            $remarks = $request->input('warden_remarks', 'Bulk rejected');
            
            foreach ($applications as $application) {
                $application->reject($warden, $remarks);
            }
            
            return redirect()->back()->with('success', count($applications) . ' applications rejected successfully.');
        } else {
            // First step - show confirmation
            $applications = RoomApplication::with(['student', 'hostel', 'roomType'])
                ->whereIn('id', $applicationIds)
                ->where('status', 'pending')
                ->get();
            
            if ($applications->isEmpty()) {
                return redirect()->back()->with('error', 'No valid applications found for rejection.');
            }
            
            return view('warden.room_allotment.bulk-reject-confirmation', compact('applications', 'applicationIds'));
        }
    }
}
