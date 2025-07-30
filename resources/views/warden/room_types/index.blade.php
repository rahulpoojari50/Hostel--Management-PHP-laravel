@extends('layouts.admin')

@section('title', 'Room Types - ' . $hostel->name)

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Room Types for {{ $hostel->name }}</h1>
    <div>
        <a href="{{ route('warden.manage-hostel.show', $hostel) }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Hostel
        </a>
        <a href="{{ route('warden.hostels.room-types.create', $hostel) }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm ml-2">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add Room Type
        </a>
    </div>
</div>

@include('components.breadcrumb', [
    'pageTitle' => 'Room Types',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'Manage Hostel', 'url' => route('warden.manage-hostel.index')],
        ['name' => $hostel->name, 'url' => route('warden.manage-hostel.show', $hostel)],
        ['name' => 'Room Types', 'url' => '']
    ]
])

<!-- Content Row -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Room Types List</h6>
            </div>
            <div class="card-body">
                @if($roomTypes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Room Type</th>
                                    <th>Capacity</th>
                                    <th>Price/Month</th>
                                    <th>Total Rooms</th>
                                    <th>Available Rooms</th>
                                    <th>Facilities</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roomTypes as $type)
                                    <tr>
                                        <td>
                                            <span class="font-weight-bold">{{ $type->type }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $type->capacity }} Student{{ $type->capacity > 1 ? 's' : '' }}</span>
                                        </td>
                                        <td>
                                            <span class="font-weight-bold text-success">₹{{ number_format($type->price_per_month, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">{{ $type->total_rooms }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $type->available_rooms > 0 ? 'success' : 'danger' }}">
                                                {{ $type->available_rooms }}
                                            </span>
                                        </td>
                                        <td>
                                            @if(is_array($type->facilities) && count($type->facilities) > 0)
                                                @foreach($type->facilities as $facility)
                                                    <span class="badge badge-secondary mr-1">{{ trim($facility) }}</span>
                                                @endforeach
                                            @elseif($type->facilities)
                                                <span class="text-muted">{{ $type->facilities }}</span>
                                            @else
                                                <span class="text-muted">No facilities listed</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('warden.hostels.room-types.show', [$hostel, $type]) }}" 
                                                   class="btn btn-info btn-sm" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('warden.hostels.room-types.edit', [$hostel, $type]) }}" 
                                                   class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('warden.hostels.room-types.destroy', [$hostel, $type]) }}" 
                                                   class="btn btn-danger btn-sm" 
                                                   title="Delete Room Type">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Summary Cards -->
                    <div class="row mt-4">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Room Types
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $roomTypes->count() }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-bed fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Total Rooms
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $roomTypes->sum('total_rooms') }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-building fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Available Rooms
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $roomTypes->sum('available_rooms') }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-door-open fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Average Price
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                ₹{{ number_format($roomTypes->avg('price_per_month'), 0) }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-rupee-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-bed fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-600">No Room Types Found</h5>
                        <p class="text-gray-500 mb-4">This hostel doesn't have any room types configured yet.</p>
                        <a href="{{ route('warden.hostels.room-types.create', $hostel) }}" class="btn btn-primary">
                            <i class="fas fa-plus fa-sm"></i> Add First Room Type
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Deleted Room Types Section -->
@if(isset($deletedRoomTypes) && $deletedRoomTypes->count() > 0)
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-secondary">
                    <i class="fas fa-trash"></i> Deleted Room Types
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Room Type</th>
                                <th>Capacity</th>
                                <th>Price/Month</th>
                                <th>Total Rooms</th>
                                <th>Deleted Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deletedRoomTypes as $type)
                                <tr class="table-secondary">
                                    <td>
                                        <span class="font-weight-bold text-muted">{{ $type->type }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $type->capacity }} Student{{ $type->capacity > 1 ? 's' : '' }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">₹{{ number_format($type->price_per_month, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $type->total_rooms }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $type->deleted_at->format('M d, Y H:i') }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <form action="{{ route('warden.hostels.room-types.restore', [$hostel, $type]) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" 
                                                        onclick="return confirm('Are you sure you want to restore this room type?')"
                                                        title="Restore">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('warden.hostels.room-types.force-delete', [$hostel, $type]) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" 
                                                        onclick="return confirm('Are you sure you want to permanently delete this room type? This action cannot be undone.')"
                                                        title="Permanently Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
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
@endsection 