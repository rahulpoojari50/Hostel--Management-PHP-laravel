<?php

namespace App\Http\Controllers\Warden;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{


    public function create($hostelId)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($hostelId);
        $roomTypes = $hostel->roomTypes;
        return view('warden.rooms.create', compact('hostel', 'roomTypes'));
    }

    public function store(Request $request)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($request->input('hostel_id'));
        $roomTypes = $hostel->roomTypes->pluck('id')->toArray();
        $validated = $request->validate([
            'room_number' => 'required|string|max:255',
            'room_type_id' => 'required|in:' . implode(',', $roomTypes),
            'floor' => 'required|integer',
            'status' => 'required|in:available,occupied,maintenance',
            'current_occupants' => 'required|integer|min:0',
            'max_occupants' => 'required|integer|min:1',
        ]);
        $validated['hostel_id'] = $hostel->id;
        Room::create($validated);
        return redirect()->route('warden.rooms.show', $hostel)->with('success', 'Room created successfully.');
    }



    public function edit($hostelId, $id)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($hostelId);
        $room = $hostel->rooms()->findOrFail($id);
        $roomTypes = $hostel->roomTypes;
        return view('warden.rooms.edit', compact('hostel', 'room', 'roomTypes'));
    }

    public function update(Request $request, $hostelId, $id)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($hostelId);
        $room = $hostel->rooms()->findOrFail($id);
        $roomTypes = $hostel->roomTypes->pluck('id')->toArray();
        $validated = $request->validate([
            'room_number' => 'required|string|max:255',
            'room_type_id' => 'required|in:' . implode(',', $roomTypes),
            'floor' => 'required|integer',
            'status' => 'required|in:available,occupied,maintenance',
            'current_occupants' => 'required|integer|min:0',
            'max_occupants' => 'required|integer|min:1',
        ]);
        $room->update($validated);
        return redirect()->route('warden.manage-hostel.show', $hostelId)->with('success', 'Room updated successfully.');
    }

    public function destroy($hostelId, $id)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($hostelId);
        $room = $hostel->rooms()->findOrFail($id);
        
        // Check if this is the final confirmation step
        if (request()->has('final_confirmation') && request('final_confirmation') === 'true') {
            // Check if room has occupants
            $occupants = $room->roomAssignments()->where('status', 'active')->count();
            if ($occupants > 0) {
                return redirect()->back()->with('error', 'Cannot delete room ' . $room->room_number . ' as it has ' . $occupants . ' occupant(s).');
            }
            
            $room->delete();
            return redirect()->route('warden.manage-hostel.show', $hostelId)->with('success', 'Room ' . $room->room_number . ' deleted successfully.');
        }
        
        // Check if this is the third confirmation step
        if (request()->has('third_confirmation') && request('third_confirmation') === 'true') {
            $step = 3;
            return view('warden.rooms.confirm_delete', compact('hostel', 'room', 'step'));
        }
        
        // Check if this is the second confirmation step
        if (request()->has('second_confirmation') && request('second_confirmation') === 'true') {
            $step = 2;
            return view('warden.rooms.confirm_delete', compact('hostel', 'room', 'step'));
        }
        
        // First confirmation step
        $step = 1;
        return view('warden.rooms.confirm_delete', compact('hostel', 'room', 'step'));
    }



    public function bulkCreate($hostelId)
    {
        $hostel = \App\Models\Hostel::findOrFail($hostelId);
        $roomTypes = \App\Models\RoomType::where('hostel_id', $hostelId)->get(['id', 'type']);
        
        // Check if a specific room type is selected
        $selectedRoomTypeId = request('room_type_id');
        $selectedRoomType = null;
        
        if ($selectedRoomTypeId) {
            $selectedRoomType = $roomTypes->where('id', $selectedRoomTypeId)->first();
        }
        
        return view('warden.rooms.bulk_create', compact('hostel', 'roomTypes', 'selectedRoomType'));
    }

    public function bulkStore(Request $request)
    {
        $hostelId = $request->input('hostel_id');
        $hostel = \App\Models\Hostel::where('warden_id', Auth::id())->findOrFail($hostelId);
        $rooms = $request->input('rooms', []);
        $created = 0;
        
        // Handle single room type bulk add
        if ($request->has('selected_room_type_id')) {
            $roomTypeId = $request->input('selected_room_type_id');
            $roomType = \App\Models\RoomType::where('hostel_id', $hostel->id)->find($roomTypeId);
            
            if (!$roomType) {
                return redirect()->back()->with('error', 'Invalid room type selected.');
            }
            
            // Validate room numbers for uniqueness
            $roomNumbers = [];
            foreach ($rooms[0] ?? [] as $roomData) {
                $roomKey = $roomData['room_number'] . '-' . $roomData['floor'];
                if (in_array($roomKey, $roomNumbers)) {
                    return redirect()->back()->with('error', "Duplicate room found: Room {$roomData['room_number']} on floor {$roomData['floor']}. Please use unique room numbers.");
                }
                $roomNumbers[] = $roomKey;
                
                // Check if room already exists
                $existingRoom = \App\Models\Room::where('hostel_id', $hostel->id)
                    ->where('room_number', $roomData['room_number'])
                    ->where('floor', $roomData['floor'])
                    ->first();
                    
                if ($existingRoom) {
                    return redirect()->back()->with('error', "Room {$roomData['room_number']} on floor {$roomData['floor']} already exists.");
                }
            }
            
            // Create rooms
            foreach ($rooms[0] ?? [] as $roomData) {
                \App\Models\Room::create([
                    'hostel_id' => $hostel->id,
                    'room_type_id' => $roomTypeId,
                    'room_number' => $roomData['room_number'],
                    'floor' => $roomData['floor'],
                    'status' => 'available',
                    'current_occupants' => 0,
                    'max_occupants' => $roomType->capacity,
                ]);
                $created++;
            }
        } else {
            // Handle multiple room types bulk add (existing functionality)
            foreach ($rooms as $typeRooms) {
                foreach ($typeRooms as $roomData) {
                    $roomType = \App\Models\RoomType::find($roomData['type_id']);
                    $capacity = $roomType ? $roomType->capacity : 1;
                    \App\Models\Room::create([
                        'hostel_id' => $hostel->id,
                        'room_type_id' => $roomData['type_id'],
                        'room_number' => $roomData['room_number'],
                        'floor' => $roomData['floor'],
                        'status' => 'available',
                        'current_occupants' => 0,
                        'max_occupants' => $capacity,
                    ]);
                    $created++;
                }
            }
        }
        
        return redirect()->route('warden.rooms.show', $hostel)->with('success', "$created rooms created successfully.");
    }

    /**
     * Enhanced rooms index - show hostel selection
     */
    public function index()
    {
        $hostels = Hostel::where('warden_id', Auth::id())->get();
        
        $pageTitle = 'Rooms Management';
        $breadcrumbs = [
            ['name' => 'Home', 'url' => url('/warden/dashboard')],
            ['name' => 'Rooms Management', 'url' => '']
        ];
        
        return view('warden.rooms.index', compact('hostels', 'pageTitle', 'breadcrumbs'));
    }

    /**
     * Show rooms for specific hostel with visual grid
     */
    public function show($hostelId)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->with(['roomTypes', 'rooms.roomType', 'rooms.roomAssignments'])->findOrFail($hostelId);
        
        // Group rooms by type for visual display
        $roomsByType = $hostel->roomTypes->map(function($roomType) {
            $rooms = $roomType->rooms->map(function($room) {
                $occupants = $room->roomAssignments->where('status', 'active')->count();
                $maxOccupants = $room->roomType->capacity;
                
                // Determine room status color
                if ($occupants == 0) {
                    $status = 'empty';
                    $color = 'success';
                } elseif ($occupants < $maxOccupants) {
                    $status = 'partial';
                    $color = 'warning';
                } else {
                    $status = 'full';
                    $color = 'danger';
                }
                
                return [
                    'id' => $room->id,
                    'room_number' => $room->room_number,
                    'floor' => $room->floor,
                    'occupants' => $occupants,
                    'max_occupants' => $maxOccupants,
                    'status' => $status,
                    'color' => $color,
                    'students' => $room->roomAssignments->where('status', 'active')->pluck('student.name')->toArray(),
                    'student_ids' => $room->roomAssignments->where('status', 'active')->pluck('student.id')->toArray()
                ];
            });
            
            return [
                'type' => $roomType,
                'rooms' => $rooms
            ];
        });
        
        $pageTitle = $hostel->name . ' - Rooms';
        $breadcrumbs = [
            ['name' => 'Home', 'url' => url('/warden/dashboard')],
            ['name' => 'Rooms Management', 'url' => route('warden.rooms.index')],
            ['name' => $hostel->name . ' Rooms', 'url' => '']
        ];
        
        return view('warden.rooms.show', compact('hostel', 'roomsByType', 'pageTitle', 'breadcrumbs'));
    }
}
