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
        return redirect()->route('warden.hostels.rooms.manage', $hostel)->with('success', 'Room created successfully.');
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
        return redirect()->route('warden.hostels.rooms.index', $hostel)->with('success', 'Room updated successfully.');
    }

    public function destroy($hostelId, $id)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($hostelId);
        $room = $hostel->rooms()->findOrFail($id);
        $room->delete();
        return redirect()->route('warden.hostels.rooms.index', $hostel)->with('success', 'Room deleted successfully.');
    }

    public function manage($hostelId)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->with(['roomTypes', 'rooms.roomType', 'rooms.roomAssignments' => function($q) {
            $q->where('status', 'active');
        }])->findOrFail($hostelId);
        // Group rooms by type
        $roomsByType = $hostel->rooms->groupBy('room_type_id');
        return view('warden.rooms.manage', compact('hostel', 'roomsByType'));
    }

    public function bulkCreate($hostelId)
    {
        $hostel = \App\Models\Hostel::findOrFail($hostelId);
        $roomTypes = \App\Models\RoomType::where('hostel_id', $hostelId)->get(['id', 'type']);
        return view('warden.rooms.bulk_create', compact('hostel', 'roomTypes'));
    }

    public function bulkStore(Request $request)
    {
        $hostelId = $request->input('hostel_id');
        $hostel = \App\Models\Hostel::findOrFail($hostelId);
        $rooms = $request->input('rooms', []);
        $created = 0;
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
        return redirect()->route('warden.hostels.rooms.manage', $hostel)->with('success', "$created rooms created successfully.");
    }

    /**
     * Enhanced rooms index - show hostel selection
     */
    public function index()
    {
        $hostels = Hostel::where('warden_id', Auth::id())->get();
        return view('warden.rooms.index', compact('hostels'));
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
                    'occupants' => $occupants,
                    'max_occupants' => $maxOccupants,
                    'status' => $status,
                    'color' => $color,
                    'students' => $room->roomAssignments->where('status', 'active')->pluck('student.name')->toArray()
                ];
            });
            
            return [
                'type' => $roomType,
                'rooms' => $rooms
            ];
        });
        
        return view('warden.rooms.show', compact('hostel', 'roomsByType'));
    }
}
