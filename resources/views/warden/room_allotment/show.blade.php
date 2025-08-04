@extends('layouts.admin')

@section('title', 'Allot Room - ' . $application->student->name)

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Allot Room to {{ $application->student->name }}</h1>
    <div>
        <a href="{{ route('warden.applications.index') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-list"></i> View All Applications
        </a>
        <a href="{{ route('warden.room-allotment.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Room Allotment
        </a>
    </div>
</div>

<!-- Application Details -->
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Application Details</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Student Information</h6>
                        <p><strong>Name:</strong> 
                            <a href="#" class="student-name-clickable text-primary" data-student-id="{{ $application->student->id }}" style="text-decoration: none; cursor: pointer;">
                                <i class="fas fa-user mr-1"></i>{{ $application->student->name }}
                            </a>
                        </p>
                        <p><strong>Email:</strong> {{ $application->student->email }}</p>
                        <p><strong>Phone:</strong> {{ $application->student->phone ?? 'Not provided' }}</p>
                        <p><strong>Address:</strong> {{ $application->student->address ?? 'Not provided' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Application Information</h6>
                        <p><strong>Hostel:</strong> {{ $application->hostel->name }}</p>
                        <p><strong>Room Type:</strong> {{ $application->roomType ? $application->roomType->type : 'Unknown' }} ({{ $application->roomType ? $application->roomType->capacity : 0 }} beds)</p>
                        <p><strong>Applied Date:</strong> {{ $application->created_at->format('M d, Y H:i') }}</p>
                        <p><strong>Status:</strong> 
                            <span class="badge badge-warning">Pending</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Room Selection -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Select Available Room</h6>
            </div>
            <div class="card-body">
                @if($availableRooms->count() > 0)
                    <form action="{{ route('warden.room-allotment.allot', $application) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="room_id">Available Rooms for {{ $selectedRoomType ? $selectedRoomType->type : ($application->roomType ? $application->roomType->type : 'Unknown Type') }}</label>
                            <select class="form-control" id="room_id" name="room_id" required>
                                <option value="">Select a room...</option>
                                @foreach($availableRooms as $room)
                                    @php
                                        $currentOccupants = $room->roomAssignments->where('status', 'active')->count();
                                        $maxOccupants = $room->max_occupants ?? ($room->roomType ? $room->roomType->capacity : 0);
                                        $availableBeds = $maxOccupants - $currentOccupants;
                                    @endphp
                                    <option value="{{ $room->id }}">
                                        Room {{ $room->room_number }} - 
                                        {{ $currentOccupants }}/{{ $maxOccupants }} beds occupied 
                                        ({{ $availableBeds }} available)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="warden_remarks">Warden Remarks (Optional)</label>
                            <textarea class="form-control" id="warden_remarks" name="warden_remarks" rows="3" 
                                      placeholder="Any additional notes or remarks..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check fa-sm"></i> Allot Room
                        </button>
                        
                        <a href="{{ route('warden.room-allotment.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times fa-sm"></i> Cancel
                        </a>
                    </form>
                @else
                    <div class="text-center text-muted">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3 text-warning"></i>
                        <h5>No Available Rooms</h5>
                        <p>There are no available {{ $selectedRoomType ? $selectedRoomType->type : $application->roomType->type }} rooms in {{ $application->hostel->name }}.</p>
                        <div class="mt-3">
                            <a href="{{ route('warden.manage-hostel.show', $application->hostel) }}" class="btn btn-primary">
                                <i class="fas fa-plus fa-sm"></i> Add More Rooms
                            </a>
                            <a href="{{ route('warden.room-allotment.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left fa-sm"></i> Back to Applications
                            </a>
                        </div>
                        <form id="differentRoomTypeForm" action="" method="GET" style="margin-top:30px;">
                            <label for="room_type_id"><strong>Choose a different room type:</strong></label>
                            <select name="room_type_id" id="room_type_id" class="form-control d-inline-block w-auto ml-2" onchange="this.form.submit()">
                                <option value="">Select Room Type</option>
                                @foreach($application->hostel->roomTypes as $type)
                                    @if($application->room_type_id != $type->id)
                                        <option value="{{ $type->id }}" {{ request('room_type_id') == $type->id ? 'selected' : '' }}>{{ $type->type }} ({{ $type->capacity ?? 0 }} beds)</option>
                                    @endif
                                @endforeach
                            </select>
                        </form>
                        @if(request('room_type_id') && $availableRooms->count() == 0)
                            <div class="alert alert-warning mt-3">No available rooms for the selected type. Please choose another.</div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Available Rooms Summary -->
@if($availableRooms->count() > 0)
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Available Rooms Overview</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Room Number</th>
                                    <th>Capacity</th>
                                    <th>Current Occupants</th>
                                    <th>Available Beds</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($availableRooms as $room)
                                    @php
                                        $currentOccupants = $room->roomAssignments->where('status', 'active')->count();
                                        $maxOccupants = $room->max_occupants ?? ($room->roomType ? $room->roomType->capacity : 0);
                                        $availableBeds = $maxOccupants - $currentOccupants;
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $room->room_number }}</strong></td>
                                        <td>{{ $maxOccupants }} beds</td>
                                        <td>{{ $currentOccupants }}</td>
                                        <td>
                                            <span class="badge badge-success">{{ $availableBeds }}</span>
                                        </td>
                                        <td>
                                            @if($currentOccupants == 0)
                                                <span class="badge badge-success">Empty</span>
                                            @else
                                                <span class="badge badge-warning">Partially Filled</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@include('components.student-profile-modal')
@endsection 