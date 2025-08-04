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
        
        $pageTitle = 'Edit Student';
        $breadcrumbs = [
            ['name' => 'Home', 'url' => url('/warden/dashboard')],
            ['name' => 'Students', 'url' => route('warden.hostels.students', $hostel->id ?? 1)],
            ['name' => 'Edit Student', 'url' => '']
        ];
        
        return view('warden.students_edit', compact('student', 'assignment', 'availableRooms', 'pageTitle', 'breadcrumbs'));
    }

    // View student profile and parent details
    public function show($id)
    {
        $student = User::where('role', 'student')->findOrFail($id);
        
        // Get the student's current active room assignment
        $assignment = $student->roomAssignments()->where('status', 'active')->with('room.hostel')->first();
        
        // Get student profile data
        $profile = $student->studentProfile;
        
        // Get parent details from both profile and user table
        $parentDetails = [
            'father_name' => $profile->father_name ?? '-',
            'father_occupation' => $profile->father_occupation ?? '-',
            'father_email' => $profile->father_email ?? '-',
            'father_mobile' => $profile->father_mobile ?? '-',
            'mother_name' => $profile->mother_name ?? '-',
            'mother_occupation' => $profile->mother_occupation ?? '-',
            'mother_email' => $profile->mother_email ?? '-',
            'mother_mobile' => $profile->mother_mobile ?? '-',
            'parent_mobile' => $student->parent_mobile ?? '-',
            'parent_email' => $student->parent_email ?? '-',
            'alternate_mobile' => $student->alternate_mobile ?? '-',
        ];
        
        $pageTitle = 'Student Profile & Parent Details';
        $breadcrumbs = [
            ['name' => 'Home', 'url' => url('/warden/dashboard')],
            ['name' => 'Students', 'url' => route('warden.hostels.students', $assignment->room->hostel->id ?? 1)],
            ['name' => $student->name, 'url' => '']
        ];
        
        return view('warden.students_show', compact('student', 'assignment', 'profile', 'parentDetails', 'pageTitle', 'breadcrumbs'));
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
        }
        
        return redirect()->back()->with('success', 'Student information updated successfully.');
    }

    // AJAX method to get student profile data for modal
    public function getProfileData($id)
    {
        try {
            \Log::info('getProfileData called with student ID: ' . $id);
            
            $student = User::where('role', 'student')->with(['studentProfile', 'roomAssignments.room.hostel'])->findOrFail($id);
            
            // Get the student's current active room assignment
            $assignment = $student->roomAssignments()->where('status', 'active')->first();
            
            // Get student profile data
            $profile = $student->studentProfile;
            
            // Prepare data for modal
            $data = [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'usn' => $student->usn ?? 'Not Available',
                'phone' => $student->phone ?? 'Not Available',
                'address' => $profile && $profile->permanent_address ? $profile->permanent_address : ($profile && $profile->present_address ? $profile->present_address : ($student->address ?? 'Not Available')),
                'photo' => $this->getStudentPhoto($student),
                'hostel_name' => $assignment && $assignment->room && $assignment->room->hostel ? $assignment->room->hostel->name : 'Not Assigned',
                'room_number' => $assignment && $assignment->room ? $assignment->room->room_number : 'Not Assigned',
                'room_type' => $assignment && $assignment->room && $assignment->room->roomType ? $assignment->room->roomType->type : 'Not Available',
                'joining_date' => $assignment ? $assignment->assigned_date->format('d M Y') : 'Not Available',
                'father_name' => $profile->father_name ?? 'Not Available',
                'father_email' => $profile->father_email ?? 'Not Available',
                'father_mobile' => $profile->father_mobile ?? 'Not Available',
                'mother_name' => $profile->mother_name ?? 'Not Available',
                'mother_email' => $profile->mother_email ?? 'Not Available',
                'mother_mobile' => $profile->mother_mobile ?? 'Not Available',
            ];
            
            \Log::info('Student profile data prepared:', $data);
            
            return response()->json($data);
        } catch (\Exception $e) {
            \Log::error('Error in getProfileData: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load student data'], 500);
        }
    }

    /**
     * Get student photo URL
     */
    private function getStudentPhoto($student)
    {
        // Check if student profile has a photo_path (preferred)
        if ($student->studentProfile && $student->studentProfile->photo_path && file_exists(storage_path('app/public/' . $student->studentProfile->photo_path))) {
            return asset('storage/' . $student->studentProfile->photo_path);
        }
        
        // Check if user has a document_path (photo)
        if ($student->document_path && file_exists(storage_path('app/public/' . $student->document_path))) {
            return asset('storage/' . $student->document_path);
        }
        
        // Check if student profile has a document_path (photo) - legacy support
        if ($student->studentProfile && $student->studentProfile->document_path && file_exists(storage_path('app/public/' . $student->studentProfile->document_path))) {
            return asset('storage/' . $student->studentProfile->document_path);
        }
        
        // Return default profile image
        return asset('admin-assets/img/undraw_profile.svg');
    }
} 