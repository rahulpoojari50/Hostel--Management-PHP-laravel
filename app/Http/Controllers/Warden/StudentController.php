<?php

namespace App\Http\Controllers\Warden;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RoomAssignment;
use App\Models\Room;
use App\Models\Hostel;

class StudentController extends Controller
{
    // Show the edit form for a student
    public function edit($id)
    {
        $student = User::where('role', 'student')->findOrFail($id);
        // Get the student's current active room assignment
        $assignment = $student->roomAssignments()->where('status', 'active')->with('room')->first();
        $hostel = null;
        $availableRooms = collect();
        if ($assignment && $assignment->room) {
            $hostel = $assignment->room->hostel;
            // Get all available rooms in the same hostel (not full, not maintenance)
            $availableRooms = $hostel->rooms()->where('status', 'available')->whereColumn('current_occupants', '<', 'max_occupants')->get();
        }
        return view('warden.students_edit', compact('student', 'assignment', 'availableRooms'));
    }

    // Update the student info
    public function update(Request $request, $id)
    {
        $student = User::where('role', 'student')->findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'room_id' => 'nullable|exists:rooms,id',
        ]);
        $student->update($validated);
        // Update room assignment if changed
        $hostelId = null;
        if ($request->filled('room_id')) {
            $assignment = $student->roomAssignments()->where('status', 'active')->first();
            if ($assignment && $assignment->room_id != $request->room_id) {
                // Decrement old room's occupants
                $oldRoom = $assignment->room;
                if ($oldRoom) {
                    $oldRoom->decrement('current_occupants');
                    if ($oldRoom->current_occupants <= 0) {
                        $oldRoom->update(['status' => 'available']);
                    }
                }
                // Assign new room
                $assignment->room_id = $request->room_id;
                $assignment->save();
                // Increment new room's occupants
                $newRoom = Room::find($request->room_id);
                if ($newRoom) {
                    $newRoom->increment('current_occupants');
                    if ($newRoom->current_occupants >= $newRoom->max_occupants) {
                        $newRoom->update(['status' => 'occupied']);
                    }
                    $hostelId = $newRoom->hostel_id;
                }
            } else if ($assignment && $assignment->room) {
                $hostelId = $assignment->room->hostel_id;
            }
        } else {
            // If not changing room, get hostel from current assignment
            $assignment = $student->roomAssignments()->where('status', 'active')->first();
            if ($assignment && $assignment->room) {
                $hostelId = $assignment->room->hostel_id;
            }
        }
        if (!$hostelId) {
            // fallback: get from request or student
            $hostelId = $request->input('hostel_id') ?? null;
        }
        return redirect()->route('warden.hostels.students', $hostelId)->with('success', 'Student info updated successfully.');
    }
} 