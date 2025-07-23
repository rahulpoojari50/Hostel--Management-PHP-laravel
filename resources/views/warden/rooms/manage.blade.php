@extends('layouts.admin')

@section('title', 'Manage Rooms for ' . $hostel->name)

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Manage Rooms – {{ $hostel->name }}</h1>
    <a href="{{ route('warden.hostels.show', $hostel) }}" class="btn btn-secondary mb-3">← Back to Hostel</a>
    <a href="{{ route('warden.hostels.rooms.bulkCreate', $hostel) }}" class="btn btn-primary mb-3">+ Bulk Add Rooms</a>

    @foreach($hostel->roomTypes as $roomType)
        <div class="card mb-4">
            <div class="card-header fw-bold">
                Room Type: {{ ucfirst($roomType->type) }} (Capacity: {{ $roomType->capacity }})
            </div>
            <div class="card-body">
                @php $rooms = $roomsByType[$roomType->id] ?? collect(); @endphp
                @if($rooms->count())
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Room Number</th>
                                    <th>Floor</th>
                                    <th>Current/Max Occupancy</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rooms as $room)
                                    <tr>
                                        <td>{{ $room->room_number }}</td>
                                        <td>Floor {{ $room->floor }}</td>
                                        <td>{{ $room->current_occupants }}/{{ $room->max_occupants }}</td>
                                        <td>
                                            @if($room->current_occupants == 0)
                                                <span class="badge bg-success">Unassigned</span>
                                            @elseif($room->current_occupants < $room->max_occupants)
                                                <span class="badge bg-warning text-dark">Partially Assigned</span>
                                            @else
                                                <span class="badge bg-danger">Full</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-muted">No rooms added for this type yet.</div>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection 