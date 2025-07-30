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
        $deletedRoomTypes = $hostel->roomTypes()->withTrashed()->whereNotNull('deleted_at')->get();
        return view('warden.room_types.index', compact('hostel', 'roomTypes', 'deletedRoomTypes'));
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
            'type' => 'required|string|max:100',
            'capacity' => 'required|integer|min:1|max:20',
            'price_per_month' => 'required|numeric|min:0',
            'total_rooms' => 'required|integer|min:1',
            'facilities' => 'nullable|string',
        ]);
        
        // Check if this room type already exists for this hostel
        $existingRoomType = $hostel->roomTypes()->where('type', $validated['type'])->first();
        
        if ($existingRoomType) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['type' => 'This room type already exists for this hostel.']);
        }
        
        // Handle facilities array
        if (!empty($validated['facilities'])) {
            $validated['facilities'] = array_map('trim', explode(',', $validated['facilities']));
        }
        
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
            'type' => 'required|string|max:100',
            'capacity' => 'required|integer|min:1|max:20',
            'price_per_month' => 'required|numeric|min:0',
            'total_rooms' => 'required|integer|min:1',
            'facilities' => 'nullable|string',
        ]);
        
        // Handle facilities array
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
        
        // Check if this is the final confirmation step
        if (request()->has('final_confirmation') && request('final_confirmation') === 'true') {
            $roomType->delete();
            return redirect()->route('warden.hostels.room-types.index', $hostel)
                ->with('success', 'Room type deleted successfully.');
        }
        
        // Check if this is the third confirmation step
        if (request()->has('third_confirmation') && request('third_confirmation') === 'true') {
            $step = 3;
            return view('warden.room_types.confirm_delete', compact('hostel', 'roomType', 'step'));
        }
        
        // Check if this is the second confirmation step
        if (request()->has('second_confirmation') && request('second_confirmation') === 'true') {
            $step = 2;
            return view('warden.room_types.confirm_delete', compact('hostel', 'roomType', 'step'));
        }
        
        // First confirmation step
        $step = 1;
        return view('warden.room_types.confirm_delete', compact('hostel', 'roomType', 'step'));
    }

    public function restore($hostelId, $id)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($hostelId);
        $roomType = $hostel->roomTypes()->withTrashed()->findOrFail($id);
        
        if ($roomType->trashed()) {
            $roomType->restore();
            return redirect()->route('warden.hostels.room-types.index', $hostel)
                ->with('success', 'Room type restored successfully.');
        }
        
        return redirect()->route('warden.hostels.room-types.index', $hostel)
            ->with('error', 'Room type is not deleted.');
    }

    public function forceDelete($hostelId, $id)
    {
        $hostel = Hostel::where('warden_id', Auth::id())->findOrFail($hostelId);
        $roomType = $hostel->roomTypes()->withTrashed()->findOrFail($id);
        
        if ($roomType->trashed()) {
            $roomType->forceDelete();
            return redirect()->route('warden.hostels.room-types.index', $hostel)
                ->with('success', 'Room type permanently deleted.');
        }
        
        return redirect()->route('warden.hostels.room-types.index', $hostel)
            ->with('error', 'Room type is not deleted.');
    }
}
