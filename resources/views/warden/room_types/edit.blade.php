@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Edit Room Type for Hostel: {{ $hostel->name }}</h1>
    <form action="{{ route('warden.hostels.room-types.update', [$hostel, $roomType]) }}" method="POST" class="bg-white shadow rounded p-6">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Type</label>
            <input type="text" name="type" class="w-full border rounded px-3 py-2" value="{{ $roomType->type }}" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Capacity</label>
            <input type="number" name="capacity" class="w-full border rounded px-3 py-2" min="1" max="20" value="{{ $roomType->capacity }}" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Price per Month</label>
            <input type="number" name="price_per_month" class="w-full border rounded px-3 py-2" value="{{ $roomType->price_per_month }}" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Total Rooms</label>
            <input type="number" name="total_rooms" class="w-full border rounded px-3 py-2" value="{{ $roomType->total_rooms }}" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Available Rooms</label>
            <input type="number" name="available_rooms" class="w-full border rounded px-3 py-2" value="{{ $roomType->available_rooms }}" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Facilities (comma separated)</label>
            <input type="text" name="facilities" class="w-full border rounded px-3 py-2" value="{{ is_array($roomType->facilities) ? implode(', ', $roomType->facilities) : $roomType->facilities }}">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update Room Type</button>
        <a href="{{ route('warden.hostels.room-types.index', $hostel) }}" class="ml-4 text-gray-600">Cancel</a>
    </form>
</div>
@endsection 