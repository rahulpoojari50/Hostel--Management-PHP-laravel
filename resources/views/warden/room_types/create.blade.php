@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Add Room Type for Hostel: {{ $hostel->name }}</h1>
    <form action="{{ route('warden.hostels.room-types.store', $hostel) }}" method="POST" class="bg-white shadow rounded p-6">
        @csrf
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Type</label>
            <input type="text" name="type" class="w-full border rounded px-3 py-2" placeholder="e.g. Single, Double, Triple, Quad" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Capacity</label>
            <input type="number" name="capacity" class="w-full border rounded px-3 py-2" min="1" max="20" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Price per Month</label>
            <input type="number" name="price_per_month" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Total Rooms</label>
            <input type="number" name="total_rooms" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Available Rooms</label>
            <input type="number" name="available_rooms" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Facilities (comma separated)</label>
            <input type="text" name="facilities" class="w-full border rounded px-3 py-2" placeholder="AC, WiFi, etc.">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Create Room Type</button>
        <a href="{{ route('warden.hostels.room-types.index', $hostel) }}" class="ml-4 text-gray-600">Cancel</a>
    </form>
</div>
@endsection 