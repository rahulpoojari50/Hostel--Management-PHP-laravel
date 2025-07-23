@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Room Type: {{ ucfirst($roomType->type) }} (Hostel: {{ $hostel->name }})</h1>
    <div class="mb-4">
        <strong>Capacity:</strong> {{ $roomType->capacity }}<br>
        <strong>Price per Month:</strong> {{ $roomType->price_per_month }}<br>
        <strong>Total Rooms:</strong> {{ $roomType->total_rooms }}<br>
        <strong>Available Rooms:</strong> {{ $roomType->available_rooms }}<br>
        <strong>Facilities:</strong> {{ is_array($roomType->facilities) ? implode(', ', $roomType->facilities) : $roomType->facilities }}<br>
    </div>
    <a href="{{ route('warden.hostels.room-types.index', $hostel) }}" class="text-blue-600">Back to Room Types</a>
</div>
@endsection 