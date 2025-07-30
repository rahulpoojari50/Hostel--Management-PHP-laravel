@extends('layouts.admin')

@section('title', $hostel->name . ' - Rooms')

@section('content')
@include('components.breadcrumb', [
    'pageTitle' => $hostel->name . ' Rooms',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'Rooms Management', 'url' => route('warden.rooms.index')],
        ['name' => $hostel->name . ' Rooms', 'url' => '']
    ]
])

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ $hostel->name }} - Rooms Overview</h1>
    <div>
        <a href="{{ route('warden.rooms.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Hostels
        </a>
    </div>
</div>

<!-- Hostel Info Card -->
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Hostel Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> {{ $hostel->name }}</p>
                        <p><strong>Type:</strong> {{ ucfirst($hostel->type) }} Hostel</p>
                        <p><strong>Address:</strong> {{ $hostel->address }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong> 
                            <span class="badge badge-{{ $hostel->status === 'active' ? 'success' : 'danger' }}">
                                {{ ucfirst($hostel->status) }}
                            </span>
                        </p>
                        <p><strong>Total Room Types:</strong> {{ $hostel->roomTypes->count() }}</p>
                        <p><strong>Total Rooms:</strong> {{ $hostel->rooms->count() }}</p>
                        <p><strong>Total Fees:</strong> ₹{{ number_format(method_exists($hostel, 'getTotalFeesForRoomType') ? $hostel->getTotalFeesForRoomType() : 0, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Legend -->
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Room Status Legend</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-success rounded p-2 mr-2" style="width: 30px; height: 30px;"></div>
                            <span>Empty (Available)</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning rounded p-2 mr-2" style="width: 30px; height: 30px;"></div>
                            <span>Partially Filled</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-danger rounded p-2 mr-2" style="width: 30px; height: 30px;"></div>
                            <span>Fully Occupied</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-secondary rounded p-2 mr-2" style="width: 30px; height: 30px;"></div>
                            <span>Maintenance</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Room Types and Visual Grid -->
@if($roomsByType->count() > 0)
    @foreach($roomsByType as $roomTypeData)
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            {{ $roomTypeData['type']->type ?? 'Unknown Type' }} Rooms 
                            <span class="badge badge-info">{{ $roomTypeData['rooms']->count() }} rooms</span>
                        </h6>
                        <div>
                            <span class="text-muted">
                                Capacity: {{ $roomTypeData['type']->capacity ?? 'N/A' }} beds | 
                                Rent: ${{ $roomTypeData['type']->price_per_month ?? 'N/A' }}/month
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($roomTypeData['rooms'] as $room)
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
                                    <div class="card border-{{ $room['color'] }} h-100">
                                        <div class="card-body text-center p-2">
                                            <div class="bg-{{ $room['color'] }} text-white rounded p-2 mb-2">
                                                <strong>{{ $room['room_number'] }}</strong>
                                            </div>
                                            <div class="small">
                                                <div class="text-muted">
                                                    {{ $room['occupants'] }}/{{ $room['max_occupants'] }} beds
                                                </div>
                                                @if($room['occupants'] > 0)
                                                    <div class="mt-1">
                                                        <small class="text-muted">Occupants:</small><br>
                                                        @foreach($room['students'] as $student)
                                                            @php
                                                                $studentId = $room['student_ids'][$loop->index] ?? null;
                                                            @endphp
                                                            @if($studentId)
                                                                <a href="#" class="student-name-clickable text-primary" data-student-id="{{ $studentId }}" style="text-decoration: none; cursor: pointer;">
                                                                    <i class="fas fa-user mr-1"></i><span class="badge badge-light">{{ $student }}</span>
                                                                </a>
                                                            @else
                                                                <span class="badge badge-light">{{ $student }}</span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-body text-center text-muted">
                    <i class="fas fa-bed fa-3x mb-3"></i>
                    <h5>No Rooms Available</h5>
                    <p>This hostel doesn't have any rooms defined yet.</p>
                    <a href="{{ route('warden.manage-hostel.show', $hostel) }}" class="btn btn-primary">
                        <i class="fas fa-plus fa-sm"></i> Add Rooms
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Summary Statistics -->
@if($roomsByType->count() > 0)
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Room Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php
                            $totalRooms = $hostel->rooms->count();
                            $emptyRooms = $hostel->rooms->filter(function($room) {
                                return $room->roomAssignments->where('status', 'active')->count() == 0;
                            })->count();
                            $partialRooms = $hostel->rooms->filter(function($room) {
                                $occupants = $room->roomAssignments->where('status', 'active')->count();
                                return $occupants > 0 && $room->roomType && $occupants < $room->roomType->capacity;
                            })->count();
                            $fullRooms = $hostel->rooms->filter(function($room) {
                                $occupants = $room->roomAssignments->where('status', 'active')->count();
                                return $room->roomType && $occupants >= $room->roomType->capacity;
                            })->count();
                        @endphp
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h4 text-success">{{ $emptyRooms }}</div>
                                <div class="text-muted">Empty Rooms</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h4 text-warning">{{ $partialRooms }}</div>
                                <div class="text-muted">Partially Filled</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h4 text-danger">{{ $fullRooms }}</div>
                                <div class="text-muted">Fully Occupied</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h4 text-primary">{{ $totalRooms }}</div>
                                <div class="text-muted">Total Rooms</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@include('components.student-profile-modal')
@endsection 