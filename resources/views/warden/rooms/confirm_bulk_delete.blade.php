@extends('layouts.admin')

@section('title', 'Confirm Bulk Delete Rooms')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Confirm Bulk Delete Rooms</h1>
    <a href="{{ route('warden.manage-hostel.show', $hostel) }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Hostel
    </a>
</div>

@include('components.breadcrumb', [
    'pageTitle' => 'Confirm Bulk Delete',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'Manage Hostel', 'url' => route('warden.manage-hostel.index')],
        ['name' => $hostel->name, 'url' => route('warden.manage-hostel.show', $hostel)],
        ['name' => 'Confirm Bulk Delete', 'url' => '']
    ]
])

<!-- Content Row -->
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Bulk Delete Rooms - Step {{ $step }} of 3
                </h6>
            </div>
            <div class="card-body">
                @if($step == 1)
                    <!-- First Confirmation -->
                    <div class="text-center mb-4">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h4 class="text-gray-800">Are you sure you want to delete {{ count($rooms) }} selected room(s)?</h4>
                        <p class="text-gray-600">This action will permanently delete the selected rooms and cannot be undone.</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Selected Rooms:</h6>
                            <ul class="list-unstyled">
                                @foreach($rooms as $room)
                                    <li><strong>{{ $room->room_number }}</strong> (Floor {{ $room->floor }}) - {{ $room->roomType->type }}</li>
                                @endforeach
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
                        <form action="{{ route('warden.hostels.rooms.bulk-delete', $hostel) }}" method="POST" class="d-inline">
                            @csrf
                            @foreach($rooms as $room)
                                <input type="hidden" name="room_ids[]" value="{{ $room->id }}">
                            @endforeach
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
                        <p class="text-gray-600">Please confirm that you really want to delete {{ count($rooms) }} room(s).</p>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6 class="font-weight-bold"><i class="fas fa-info-circle"></i> Important Information:</h6>
                        <ul class="mb-0">
                            <li>{{ count($rooms) }} rooms will be permanently deleted</li>
                            <li>All room data and history will be lost</li>
                            <li>This action cannot be undone or recovered</li>
                            <li>Make sure all rooms are empty before proceeding</li>
                        </ul>
                    </div>
                    
                    <div class="text-center mt-4">
                        <form action="{{ route('warden.hostels.rooms.bulk-delete', $hostel) }}" method="POST" class="d-inline">
                            @csrf
                            @foreach($rooms as $room)
                                <input type="hidden" name="room_ids[]" value="{{ $room->id }}">
                            @endforeach
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
                        <p class="text-gray-600">This is your last chance to cancel the bulk deletion.</p>
                    </div>
                    
                    <div class="alert alert-danger">
                        <h6 class="font-weight-bold"><i class="fas fa-exclamation-triangle"></i> Final Warning:</h6>
                        <ul class="mb-0">
                            <li>{{ count($rooms) }} rooms will be permanently deleted</li>
                            <li>All room data and history will be lost</li>
                            <li>This action cannot be undone or recovered</li>
                            <li>Are you absolutely sure you want to proceed?</li>
                        </ul>
                    </div>
                    
                    <div class="text-center mt-4">
                        <form action="{{ route('warden.hostels.rooms.bulk-delete', $hostel) }}" method="POST" class="d-inline">
                            @csrf
                            @foreach($rooms as $room)
                                <input type="hidden" name="room_ids[]" value="{{ $room->id }}">
                            @endforeach
                            <input type="hidden" name="final_confirmation" value="true">
                            <button type="submit" class="btn btn-danger btn-lg">
                                <i class="fas fa-trash"></i> Yes, Delete {{ count($rooms) }} Room(s)
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