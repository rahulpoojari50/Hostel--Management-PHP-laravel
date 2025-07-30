@extends('layouts.admin')

@section('title', 'Confirm Delete - ' . $roomType->type)

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Confirm Delete Room Type</h1>
    <a href="{{ route('warden.hostels.room-types.index', $hostel) }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Room Types
    </a>
</div>

@include('components.breadcrumb', [
    'pageTitle' => 'Confirm Delete',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'Manage Hostel', 'url' => route('warden.manage-hostel.index')],
        ['name' => $hostel->name, 'url' => route('warden.manage-hostel.show', $hostel)],
        ['name' => 'Room Types', 'url' => route('warden.hostels.room-types.index', $hostel)],
        ['name' => 'Confirm Delete', 'url' => '']
    ]
])

<!-- Content Row -->
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Delete Room Type - Step {{ $step }} of 3
                </h6>
            </div>
            <div class="card-body">
                @if($step == 1)
                    <!-- First Confirmation -->
                    <div class="text-center mb-4">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h4 class="text-gray-800">Are you sure you want to delete this room type?</h4>
                        <p class="text-gray-600">This action will move the room type to the deleted items list.</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Room Type Details:</h6>
                            <ul class="list-unstyled">
                                <li><strong>Type:</strong> {{ $roomType->type }}</li>
                                <li><strong>Capacity:</strong> {{ $roomType->capacity }} students</li>
                                <li><strong>Price:</strong> ₹{{ number_format($roomType->price_per_month, 2) }}/month</li>
                                <li><strong>Total Rooms:</strong> {{ $roomType->total_rooms }}</li>
                                <li><strong>Available Rooms:</strong> {{ $roomType->available_rooms }}</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-warning">Warning:</h6>
                            <ul class="text-warning">
                                <li>This action can be undone</li>
                                <li>Students with this room type will not be affected</li>
                                <li>You can restore it later if needed</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <form action="{{ route('warden.hostels.room-types.destroy', [$hostel, $roomType]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="second_confirmation" value="true">
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="fas fa-check"></i> Yes, Continue to Step 2
                            </button>
                        </form>
                        <a href="{{ route('warden.hostels.room-types.index', $hostel) }}" class="btn btn-secondary btn-lg ml-2">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                    
                @elseif($step == 2)
                    <!-- Second Confirmation -->
                    <div class="text-center mb-4">
                        <i class="fas fa-exclamation-triangle fa-3x text-orange mb-3"></i>
                        <h4 class="text-gray-800">Second Confirmation Required</h4>
                        <p class="text-gray-600">Please confirm that you really want to delete this room type.</p>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6 class="font-weight-bold"><i class="fas fa-info-circle"></i> Important Information:</h6>
                        <ul class="mb-0">
                            <li>This room type has {{ $roomType->total_rooms }} total rooms</li>
                            <li>{{ $roomType->available_rooms }} rooms are currently available</li>
                            <li>Any existing room assignments will remain active</li>
                            <li>You can restore this room type from the deleted items list</li>
                        </ul>
                    </div>
                    
                    <div class="text-center mt-4">
                        <form action="{{ route('warden.hostels.room-types.destroy', [$hostel, $roomType]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="third_confirmation" value="true">
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="fas fa-check"></i> Yes, Continue to Step 3
                            </button>
                        </form>
                        <a href="{{ route('warden.hostels.room-types.index', $hostel) }}" class="btn btn-secondary btn-lg ml-2">
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
                            <li>This action will move the room type to deleted items</li>
                            <li>It will no longer appear in the main room types list</li>
                            <li>You can restore it later if needed</li>
                            <li>Are you absolutely sure you want to proceed?</li>
                        </ul>
                    </div>
                    
                    <div class="text-center mt-4">
                        <form action="{{ route('warden.hostels.room-types.destroy', [$hostel, $roomType]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="final_confirmation" value="true">
                            <button type="submit" class="btn btn-danger btn-lg">
                                <i class="fas fa-trash"></i> Yes, Delete Room Type
                            </button>
                        </form>
                        <a href="{{ route('warden.hostels.room-types.index', $hostel) }}" class="btn btn-secondary btn-lg ml-2">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 