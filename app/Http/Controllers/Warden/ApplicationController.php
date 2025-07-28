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
        return view('warden.applications.index', compact('applications'));
    }

    public function show($id)
    {
        $warden = Auth::user();
        $hostelIds = Hostel::where('warden_id', $warden->id)->pluck('id');
        $application = RoomApplication::with(['student', 'hostel', 'roomType', 'processedBy'])
            ->whereIn('hostel_id', $hostelIds)
            ->findOrFail($id);
        $availableRooms = [];
        if ($application->status === 'pending') {
            $availableRooms = \App\Models\Room::where('hostel_id', $application->hostel_id)
                ->where('room_type_id', $application->room_type_id)
                ->where('status', 'available')
                ->whereColumn('current_occupants', '<', 'max_occupants')
                ->orderBy('room_number')
                ->get();
        }
        return view('warden.applications.show', compact('application', 'availableRooms'));
    }

    public function update(Request $request, $id)
    {
        $warden = Auth::user();
        $hostelIds = Hostel::where('warden_id', $warden->id)->pluck('id');
        $application = RoomApplication::whereIn('hostel_id', $hostelIds)->findOrFail($id);
        $action = $request->input('action');
        $remarks = $request->input('warden_remarks');

        if ($application->status !== 'pending') {
            return redirect()->back()->with('error', 'Application already processed.');
        }

        if ($action === 'approve') {
            // Check if room_id is provided
            if (!$request->has('room_id') || empty($request->input('room_id'))) {
                // Redirect to room allotment page for room selection
                return redirect()->route('warden.room-allotment.show', $application->id)
                    ->with('info', 'Please select a room to complete the approval process.');
            }
            
            $request->validate([
                'room_id' => ['required', 'exists:rooms,id'],
            ]);
            $room = \App\Models\Room::findOrFail($request->room_id);
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
            $application->approve($warden, $remarks);
            return redirect()->route('warden.applications.index')->with('success', 'Application approved and room assigned.');
        } elseif ($action === 'reject') {
            $application->reject($warden, $remarks);
            return redirect()->route('warden.applications.index')->with('success', 'Application rejected.');
        }

        return redirect()->back()->with('error', 'Invalid action.');
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
            ['name' => 'Hostel Dashboard', 'url' => url('/warden/dashboard')],
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
            ->where('status', 'pending')
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
        } else if ($application->status === 'pending' && $application->room_type_id && $application->roomType && $application->roomType->type) {
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
            ->where('status', 'pending')
            ->findOrFail($id);
        
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'warden_remarks' => 'nullable|string|max:500',
        ]);
        
        $room = \App\Models\Room::findOrFail($validated['room_id']);
        
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
            'hostel_id' => $room->hostel_id,
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
        
        return redirect()->route('warden.room-allotment.index')
            ->with('success', 'Room allotted successfully to ' . $application->student->name);
    }

    // Bulk reject room applications
    public function bulkReject(Request $request)
    {
        $warden = Auth::user();
        $applicationIds = $request->input('application_ids', []);
        if (empty($applicationIds)) {
            return redirect()->back()->with('error', 'No applications selected for rejection.');
        }
        $applications = RoomApplication::whereIn('id', $applicationIds)->where('status', 'pending')->get();
        foreach ($applications as $application) {
            $application->reject($warden, 'Bulk rejected');
        }
        return redirect()->back()->with('success', count($applications) . ' applications rejected successfully.');
    }
}
