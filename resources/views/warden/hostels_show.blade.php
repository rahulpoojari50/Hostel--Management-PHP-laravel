@extends('layouts.admin')

@section('title', 'Hostel Details')

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Hostel Details – {{ $hostel->name }}</h1>
        <div>
            <a href="#students-list" class="btn btn-info btn-sm mr-2"><i class="fas fa-users"></i> View Students</a>
            <form action="{{ route('warden.hostels.destroy', $hostel->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete hostel {{ $hostel->name }}?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete Hostel</button>
            </form>
        </div>
    </div>

    @include('components.breadcrumb', [
        'pageTitle' => 'Hostel Details',
        'breadcrumbs' => [
            ['name' => 'Home', 'url' => url('/')],
            ['name' => 'Hostels Management', 'url' => route('warden.hostels.index')],
            ['name' => 'Hostel Details', 'url' => '']
        ]
    ])
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">General Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-6"><strong>Name:</strong> {{ $hostel->name }}</div>
                        <div class="col-md-6"><strong>Type:</strong> {{ ucfirst($hostel->type) }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6"><strong>Status:</strong> {{ ucfirst($hostel->status) }}</div>
                        <div class="col-md-6"><strong>Warden:</strong> {{ $hostel->warden->name ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-12"><strong>Address:</strong> {{ $hostel->address }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-12"><strong>Description:</strong> {{ $hostel->description }}</div>
                    </div>
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Room Types & Occupancy</h6>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Type</th><th>Capacity</th><th>Rent/month</th><th>Total Rooms</th><th>Occupied</th><th>Vacant</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hostel->roomTypes as $type)
                                @php
                                    $totalRooms = $type->rooms->count();
                                    $occupied = $type->rooms->filter(fn($r) => $r->status === 'occupied')->count();
                                    $vacant = $totalRooms - $occupied;
                                @endphp
                                <tr>
                                    <td>{{ $type->type }}</td>
                                    <td>{{ $type->capacity }}</td>
                                    <td>₹{{ $type->price_per_month }}</td>
                                    <td>{{ $totalRooms }}</td>
                                    <td>{{ $occupied }}</td>
                                    <td>{{ $vacant }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card shadow mb-4" id="students-list">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Students Allotted Rooms</h6>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th><th>USN</th><th>Email</th><th>Room Type</th><th>Room No</th><th>Floor No</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                @php
                                    $assignment = $student->roomAssignments->where('room.hostel_id', $hostel->id)->first();
                                @endphp
                                <tr>
                                    <td>
                                        <a href="#" class="student-name-clickable text-primary" data-student-id="{{ $student->id }}" style="text-decoration: none; cursor: pointer;">
                                            <i class="fas fa-user mr-1"></i>{{ $student->name }}
                                        </a>
                                    </td>
                                    <td>{{ $student->usn ?? '-' }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>{{ $assignment->room->roomType->type ?? '-' }}</td>
                                    <td>{{ $assignment->room->room_number ?? '-' }}</td>
                                    <td>{{ $assignment->room->floor ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">No students allotted rooms.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                {{ $students->links('pagination::bootstrap-4') }}
            </div>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Meal Menu</h6>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="thead-light">
                            <tr><th>Day</th><th>Breakfast</th><th>Lunch</th><th>Snacks</th><th>Dinner</th></tr>
                        </thead>
                        <tbody>
                            @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                                <tr>
                                    <td class="font-weight-bold">{{ $day }}</td>
                                    @foreach(['breakfast','lunch','snacks','dinner'] as $meal)
                                        <td>{{ $hostel->menu[$day][$meal] ?? '-' }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Facilities</h6>
                </div>
                <div class="card-body">
                    {!! nl2br(e($hostel->description)) !!}
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Room List</h6>
                </div>
                <div class="card-body table-responsive">
                    <table class="table mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Room No.</th><th>Type</th><th>Status</th><th>Occupants</th><th>Max</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hostel->rooms as $room)
                                <tr>
                                    <td>{{ $room->room_number }}</td>
                                    <td>{{ $room->roomType->type ?? '-' }}</td>
                                    <td class="text-capitalize">{{ $room->status }}</td>
                                    <td>{{ $room->current_occupants }}</td>
                                    <td>{{ $room->max_occupants }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center">No rooms found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('components.student-profile-modal')
@endsection
