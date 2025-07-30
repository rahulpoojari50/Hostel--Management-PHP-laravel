@extends('layouts.admin')

@section('title', 'Add Room Type - ' . $hostel->name)

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Add Room Type for {{ $hostel->name }}</h1>
    <div>
        <a href="{{ route('warden.hostels.room-types.index', $hostel) }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Room Types
        </a>
    </div>
</div>

@include('components.breadcrumb', [
    'pageTitle' => 'Add Room Type',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'Manage Hostel', 'url' => route('warden.manage-hostel.index')],
        ['name' => $hostel->name, 'url' => route('warden.manage-hostel.show', $hostel)],
        ['name' => 'Room Types', 'url' => route('warden.hostels.room-types.index', $hostel)],
        ['name' => 'Add Room Type', 'url' => '']
    ]
])

<!-- Content Row -->
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Room Type Information</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('warden.hostels.room-types.store', $hostel) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="roomType">Custom Room Type Name <span class="text-danger">*</span></label>
                        <input type="text" name="type" class="form-control" placeholder="e.g. Deluxe Single, Premium Double, Executive Suite, etc." required>
                        <small class="form-text text-muted">Enter a unique name for your custom room type</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="capacity">Capacity (Number of Students per Room) <span class="text-danger">*</span></label>
                        <input type="number" name="capacity" class="form-control" min="1" max="20" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="price_per_month">Price per Month (₹) <span class="text-danger">*</span></label>
                        <input type="number" name="price_per_month" class="form-control" min="0" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="total_rooms">Total Rooms <span class="text-danger">*</span></label>
                        <input type="number" name="total_rooms" class="form-control" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="facilities">Facilities (comma separated)</label>
                        <input type="text" name="facilities" class="form-control" placeholder="AC, WiFi, Attached Bathroom, etc.">
                        <small class="form-text text-muted">Enter facilities separated by commas (e.g., AC, WiFi, Attached Bathroom)</small>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Room Type
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
                
                <div>
                    <h6 class="font-weight-bold text-primary">Facilities Examples:</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-snowflake text-info mr-2"></i>AC, WiFi, Attached Bathroom</li>
                        <li><i class="fas fa-wifi text-info mr-2"></i>WiFi, Study Table, Wardrobe</li>
                        <li><i class="fas fa-shower text-info mr-2"></i>Attached Bathroom, Geyser</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 