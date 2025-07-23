@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Application Details</h1>
    <div class="mb-4">
        <strong>Student:</strong> {{ $application->student->name ?? '-' }}<br>
        <strong>Email:</strong> {{ $application->student->email ?? '-' }}<br>
        <strong>Phone:</strong> {{ $application->student->phone ?? '-' }}<br>
        <strong>Address:</strong> {{ $application->student->address ?? '-' }}<br>
    </div>
    <div class="mb-4">
        <strong>Hostel:</strong> {{ $application->hostel->name ?? '-' }}<br>
        <strong>Room Type:</strong> {{ $application->roomType->type ?? '-' }}<br>
        <strong>Application Date:</strong> {{ $application->application_date }}<br>
        <strong>Status:</strong> {{ ucfirst($application->status) }}<br>
        <strong>Warden Remarks:</strong> {{ $application->warden_remarks ?? '-' }}<br>
        <strong>Processed By:</strong> {{ $application->processedBy->name ?? '-' }}<br>
        <strong>Processed At:</strong> {{ $application->processed_at ?? '-' }}<br>
    </div>
    @if($application->status == 'pending')
        <form action="{{ route('warden.applications.update', $application) }}" method="POST" class="mb-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="action" value="approve">
            <label class="block mb-1 font-semibold">Select Room Number <span class="text-red-500">*</span></label>
            <select name="room_id" class="w-full border rounded px-3 py-2 mb-2" required>
                <option value="">-- Select Room --</option>
                @foreach($availableRooms as $room)
                    <option value="{{ $room->id }}">Room {{ $room->room_number }} (Floor {{ $room->floor }}, {{ $room->current_occupants }}/{{ $room->max_occupants }})</option>
                @endforeach
            </select>
            <label class="block mb-1 font-semibold">Warden Remarks (optional)</label>
            <textarea name="warden_remarks" class="w-full border rounded px-3 py-2 mb-2"></textarea>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Approve</button>
        </form>
        <form action="{{ route('warden.applications.update', $application) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="action" value="reject">
            <label class="block mb-1 font-semibold">Warden Remarks (optional)</label>
            <textarea name="warden_remarks" class="w-full border rounded px-3 py-2 mb-2"></textarea>
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Reject</button>
        </form>
    @endif
    <a href="{{ route('warden.applications.index') }}" class="text-blue-600 mt-4 inline-block">Back to Applications</a>
</div>
@endsection 