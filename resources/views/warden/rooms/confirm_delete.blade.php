@extends('layouts.admin')

@section('title', 'Confirm Delete - Room ' . $room->room_number)

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Confirm Delete Room</h1>
    <a href="{{ route('warden.rooms.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Rooms
    </a>
</div>

@include('components.breadcrumb', [
    'pageTitle' => 'Confirm Delete',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'Manage Hostel', 'url' => route('warden.manage-hostel.index')],
        ['name' => $hostel->name, 'url' => route('warden.manage-hostel.show', $hostel)],
        ['name' => 'Confirm Delete Room', 'url' => '']
    ]
])

<!-- Content Row -->
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Delete Room - Step {{ $step }} of 3
                </h6>
            </div>
            <div class="card-body">
                @if($step == 1)
                    <!-- First Confirmation -->
                    <div class="text-center mb-4">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h4 class="text-gray-800">Are you sure you want to delete this room?</h4>
                        <p class="text-gray-600">This action will permanently delete the room and cannot be undone.</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Room Details:</h6>
                            <ul class="list-unstyled">
                                <li><strong>Room Number:</strong> {{ $room->room_number }}</li>
                                <li><strong>Floor:</strong> {{ $room->floor }}</li>
                                <li><strong>Room Type:</strong> {{ $room->roomType->type }}</li>
                                <li><strong>Capacity:</strong> {{ $room->max_occupants }} students</li>
                                <li><strong>Current Occupants:</strong> {{ $room->current_occupants }}</li>
                                <li><strong>Status:</strong> 
                                    <span class="badge badge-{{ $room->status == 'available' ? 'success' : ($room->status == 'occupied' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($room->status) }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-warning">Warning:</h6>
                            <ul class="text-warning">
                                <li>This action cannot be undone</li>
                                <li>All room data will be permanently lost</li>
                                <li>Any active assignments will be affected</li>
                                <li>Make sure no students are currently assigned</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <form action="{{ route('warden.hostels.rooms.destroy', [$hostel, $room]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="second_confirmation" value="true">
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="fas fa-check"></i> Yes, Continue to Step 2
                            </button>
                        </form>
                        <a href="{{ route('warden.manage-hostel.show', $hostel) }}" class="btn btn-secondary btn-lg ml-2">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                    
                @elseif($step == 2)
                    <!-- Second Confirmation -->
                    <div class="text-center mb-4">
                        <i class="fas fa-exclamation-triangle fa-3x text-orange mb-3"></i>
                        <h4 class="text-gray-800">Second Confirmation Required</h4>
                        <p class="text-gray-600">Please confirm that you really want to delete this room.</p>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6 class="font-weight-bold"><i class="fas fa-info-circle"></i> Important Information:</h6>
                        <ul class="mb-0">
                            <li>Room {{ $room->room_number }} on floor {{ $room->floor }}</li>
                            <li>Room type: {{ $room->roomType->type }}</li>
                            <li>Current occupants: {{ $room->current_occupants }}/{{ $room->max_occupants }}</li>
                            <li>This action will permanently remove all room data</li>
                            <li>No recovery is possible after deletion</li>
                        </ul>
                    </div>
                    
                    <div class="text-center mt-4">
                        <form action="{{ route('warden.hostels.rooms.destroy', [$hostel, $room]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="third_confirmation" value="true">
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="fas fa-check"></i> Yes, Continue to Step 3
                            </button>
                        </form>
                        <a href="{{ route('warden.manage-hostel.show', $hostel) }}" class="btn btn-secondary btn-lg ml-2">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                    
                @elseif($step == 3)
                    <!-- Final Confirmation -->
                    <div class="text-center mb-4">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <h4 class="text-gray-800">Final Confirmation</h4>
                        <p class="text-gray-600">This is your last chance to cancel the deletion.</p>
                    </div>
                    
                    <div class="alert alert-danger">
                        <h6 class="font-weight-bold"><i class="fas fa-exclamation-triangle"></i> Final Warning:</h6>
                        <ul class="mb-0">
                            <li>Room {{ $room->room_number }} will be permanently deleted</li>
                            <li>All room data and history will be lost</li>
                            <li>This action cannot be undone or recovered</li>
                            <li>Are you absolutely sure you want to proceed?</li>
                        </ul>
                    </div>
                    
                    <div class="text-center mt-4">
                        <form action="{{ route('warden.hostels.rooms.destroy', [$hostel, $room]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="final_confirmation" value="true">
                            <button type="submit" class="btn btn-danger btn-lg">
                                <i class="fas fa-trash"></i> Yes, Delete Room
                            </button>
                        </form>
                        <a href="{{ route('warden.manage-hostel.show', $hostel) }}" class="btn btn-secondary btn-lg ml-2">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 