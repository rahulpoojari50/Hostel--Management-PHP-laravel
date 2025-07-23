<?php

namespace App\Http\Controllers\Warden;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hostel;
use App\Models\RoomType;
use Illuminate\Support\Facades\Auth;

class RoomTypeController extends Controller
{
    public function index($hostelId)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($hostelId);
        $roomTypes = $hostel->roomTypes;
        return view('warden.room_types.index', compact('hostel', 'roomTypes'));
    }

    public function create($hostelId)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($hostelId);
        return view('warden.room_types.create', compact('hostel'));
    }

    public function store(Request $request, $hostelId)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($hostelId);
        $validated = $request->validate([
            'type' => 'required|string|max:50',
            'capacity' => 'required|integer|min:1|max:20',
        ]);
        $validated['hostel_id'] = $hostel->id;
        RoomType::create($validated);
        return redirect()->back()->with('success', 'Room type created successfully.');
    }

    public function show($hostelId, $id)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($hostelId);
        $roomType = $hostel->roomTypes()->findOrFail($id);
        return view('warden.room_types.show', compact('hostel', 'roomType'));
    }

    public function edit($hostelId, $id)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($hostelId);
        $roomType = $hostel->roomTypes()->findOrFail($id);
        return view('warden.room_types.edit', compact('hostel', 'roomType'));
    }

    public function update(Request $request, $hostelId, $id)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($hostelId);
        $roomType = $hostel->roomTypes()->findOrFail($id);
        $validated = $request->validate([
            'type' => 'required|in:single,double,triple,quad',
            'capacity' => 'required|integer|min:1|max:4',
            'price_per_month' => 'required|numeric|min:0',
            'total_rooms' => 'required|integer|min:1',
            'available_rooms' => 'required|integer|min:0',
            'facilities' => 'nullable|string',
        ]);
        if (!empty($validated['facilities'])) {
            $validated['facilities'] = array_map('trim', explode(',', $validated['facilities']));
        }
        $roomType->update($validated);
        return redirect()->route('warden.hostels.room-types.index', $hostel)->with('success', 'Room type updated successfully.');
    }

    public function destroy($hostelId, $id)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($hostelId);
        $roomType = $hostel->roomTypes()->findOrFail($id);
        $roomType->delete();
        return redirect()->route('warden.hostels.room-types.index', $hostel)->with('success', 'Room type deleted successfully.');
    }
}
