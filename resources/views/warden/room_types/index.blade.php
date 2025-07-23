@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Room Types for Hostel: {{ $hostel->name }}</h1>
    <a href="{{ route('warden.hostels.room-types.create', $hostel) }}" class="bg-blue-600 text-white px-4 py-2 rounded mb-4 inline-block">Add Room Type</a>
    <table class="min-w-full bg-white shadow rounded">
        <thead>
            <tr>
                <th class="px-4 py-2">Type</th>
                <th class="px-4 py-2">Capacity</th>
                <th class="px-4 py-2">Price/Month</th>
                <th class="px-4 py-2">Total Rooms</th>
                <th class="px-4 py-2">Available Rooms</th>
                <th class="px-4 py-2">Facilities</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($roomTypes as $type)
                <tr>
                    <td class="border px-4 py-2 capitalize">{{ $type->type }}</td>
                    <td class="border px-4 py-2">{{ $type->capacity }}</td>
                    <td class="border px-4 py-2">{{ $type->price_per_month }}</td>
                    <td class="border px-4 py-2">{{ $type->total_rooms }}</td>
                    <td class="border px-4 py-2">{{ $type->available_rooms }}</td>
                    <td class="border px-4 py-2">{{ is_array($type->facilities) ? implode(', ', $type->facilities) : $type->facilities }}</td>
                    <td class="border px-4 py-2">
                        <a href="{{ route('warden.hostels.room-types.show', [$hostel, $type]) }}" class="text-blue-600">View</a> |
                        <a href="{{ route('warden.hostels.room-types.edit', [$hostel, $type]) }}" class="text-yellow-600">Edit</a> |
                        <form action="{{ route('warden.hostels.room-types.destroy', [$hostel, $type]) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600" onclick="return confirm('Delete this room type?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center py-4">No room types found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <a href="{{ route('warden.hostels.show', $hostel) }}" class="text-blue-600 mt-4 inline-block">Back to Hostel</a>
</div>
@endsection 