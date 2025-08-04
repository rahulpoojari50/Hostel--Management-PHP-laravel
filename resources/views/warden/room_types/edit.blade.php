@extends('layouts.admin')

@section('title', 'Edit Room Type - ' . $hostel->name)

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Room Type for {{ $hostel->name }}</h1>
    <div>
        <a href="{{ route('warden.hostels.room-types.index', $hostel) }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Room Types
        </a>
    </div>
</div>

@include('components.breadcrumb', [
    'pageTitle' => $pageTitle,
    'breadcrumbs' => $breadcrumbs
])

<!-- Content Row -->
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Edit Room Type Information</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('warden.hostels.room-types.update', [$hostel, $roomType]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="roomType">Custom Room Type Name <span class="text-danger">*</span></label>
                        <input type="text" name="type" class="form-control" value="{{ $roomType->type }}" required>
                        <small class="form-text text-muted">Enter a unique name for your custom room type</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="capacity">Capacity (Number of Students per Room) <span class="text-danger">*</span></label>
                        <input type="number" name="capacity" class="form-control" min="1" max="20" value="{{ $roomType->capacity }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="price_per_month">Price per Month (₹) <span class="text-danger">*</span></label>
                        <input type="number" name="price_per_month" class="form-control" min="0" step="0.01" value="{{ $roomType->price_per_month }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="total_rooms">Total Rooms <span class="text-danger">*</span></label>
                        <input type="number" name="total_rooms" class="form-control" min="1" value="{{ $roomType->total_rooms }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="facilities">Facilities (comma separated)</label>
                        <input type="text" name="facilities" class="form-control" 
                               value="{{ is_array($roomType->facilities) ? implode(', ', $roomType->facilities) : $roomType->facilities }}" 
                               placeholder="AC, WiFi, Attached Bathroom, etc.">
                        <small class="form-text text-muted">Enter facilities separated by commas (e.g., AC, WiFi, Attached Bathroom)</small>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Room Type
                        </button>
                        <a href="{{ route('warden.hostels.room-types.index', $hostel) }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Current Room Type Details</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="font-weight-bold text-primary">Room Type:</h6>
                    <p class="text-gray-800">{{ $roomType->type }}</p>
                </div>
                
                <div class="mb-3">
                    <h6 class="font-weight-bold text-primary">Capacity:</h6>
                    <p class="text-gray-800">{{ $roomType->capacity }} Student{{ $roomType->capacity > 1 ? 's' : '' }}</p>
                </div>
                
                <div class="mb-3">
                    <h6 class="font-weight-bold text-primary">Price:</h6>
                    <p class="text-gray-800">₹{{ number_format($roomType->price_per_month, 2) }}/month</p>
                </div>
                
                <div class="mb-3">
                    <h6 class="font-weight-bold text-primary">Total Rooms:</h6>
                    <p class="text-gray-800">{{ $roomType->total_rooms }}</p>
                </div>
                
                <div class="mb-3">
                    <h6 class="font-weight-bold text-primary">Available Rooms:</h6>
                    <p class="text-gray-800">{{ $roomType->available_rooms }}</p>
                </div>
                
                @if(is_array($roomType->facilities) && count($roomType->facilities) > 0)
                <div>
                    <h6 class="font-weight-bold text-primary">Current Facilities:</h6>
                    <ul class="list-unstyled">
                        @foreach($roomType->facilities as $facility)
                            <li><i class="fas fa-check text-success mr-2"></i>{{ trim($facility) }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
        
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Tips</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="font-weight-bold text-primary">Room Type Guidelines:</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success mr-2"></i>Single: 1 student per room</li>
                        <li><i class="fas fa-check text-success mr-2"></i>Double: 2 students per room</li>
                        <li><i class="fas fa-check text-success mr-2"></i>Triple: 3 students per room</li>
                        <li><i class="fas fa-check text-success mr-2"></i>Four+: 4+ students per room</li>
                    </ul>
                </div>
                
                <div class="mb-3">
                    <h6 class="font-weight-bold text-primary">Pricing Suggestions:</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-rupee-sign text-success mr-2"></i>Single: ₹40,000/month</li>
                        <li><i class="fas fa-rupee-sign text-success mr-2"></i>Double: ₹25,000/month</li>
                        <li><i class="fas fa-rupee-sign text-success mr-2"></i>Triple: ₹20,000/month</li>
                        <li><i class="fas fa-rupee-sign text-success mr-2"></i>Four+: ₹15,000/month</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 