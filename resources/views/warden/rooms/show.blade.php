@extends('layouts.admin')

@section('title', $hostel->name . ' - Rooms')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <!-- Breadcrumb Navigation -->
        @include('components.breadcrumb-nav', ['breadcrumbs' => $breadcrumbs])
    </div>
    <div>
        <a href="{{ route('warden.rooms.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Hostels
        </a>
    </div>
</div>

<!-- Page Title -->
<div class="mb-4">
    <h5 class="mb-0 text-gray-800">{{ $hostel->name }} - Rooms Overview</h5>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-times-circle"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

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

<!-- Add Room Type Section -->
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Add Room Type</h6>
            </div>
            <div class="card-body">
                @if(!isset($pendingRoomType))
                <form action="{{ route('warden.manage-hostel.room-types.store', $hostel) }}" method="POST" id="roomTypeForm">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="type">Room Type</label>
                            <input type="text" class="form-control" id="type" name="type" placeholder="e.g. Single, Double, Triple, Quad" required>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="capacity">Capacity</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" min="1" required>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="number_of_rooms">Number of Rooms</label>
                            <input type="number" class="form-control" id="number_of_rooms" name="number_of_rooms" min="1" max="100" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="price_per_month">Rent</label>
                            <input type="number" class="form-control" id="price_per_month" name="price_per_month" min="0" step="0.01" required>
                        </div>
                        <div class="form-group col-md-2 align-self-end">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-plus fa-sm"></i> Add Room Type
                            </button>
                        </div>
                    </div>
                </form>
                @endif

                @if(isset($pendingRoomType) && $pendingRoomCount)
                <!-- Step 3: Assign Room Numbers and Floor Numbers -->
                <form action="{{ route('warden.manage-hostel.rooms.store', $hostel) }}" method="POST">
                    @csrf
                    <input type="hidden" name="room_type_id" value="{{ $pendingRoomTypeId }}">
                    <div class="mb-3">
                        <strong>Room Type:</strong> {{ $pendingRoomType['type'] }} | 
                        <strong>Capacity:</strong> {{ $pendingRoomType['capacity'] }} | 
                        <strong>Rent:</strong> ₹{{ $pendingRoomType['price_per_month'] }} | 
                        <strong>Total Rooms:</strong> {{ $pendingRoomCount }}
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Room No.</th>
                                    <th>Floor No.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for($i=0; $i<$pendingRoomCount; $i++)
                                <tr>
                                    <td><input type="text" name="rooms[{{ $i }}][room_number]" class="form-control" required></td>
                                    <td><input type="text" name="rooms[{{ $i }}][floor_number]" class="form-control" required></td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save fa-sm"></i> Save Rooms
                    </button>
                </form>
                @endif

                @if(isset($pendingAddRooms))
                <!-- Step 2: Add Room Details Form -->
                <div class="card mt-4 border-primary">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-bed"></i> Add Room Details - Step 2
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6 class="font-weight-bold">Room Type Information:</h6>
                            <ul class="mb-0">
                                <li><strong>Room Type:</strong> {{ $pendingAddRooms['room_type_name'] }}</li>
                                <li><strong>Capacity:</strong> {{ $pendingAddRooms['capacity'] }} students</li>
                                <li><strong>Price:</strong> ₹{{ number_format($pendingAddRooms['price_per_month'], 2) }}/month</li>
                                <li><strong>Number of Rooms:</strong> {{ $pendingAddRooms['number_of_rooms'] }}</li>
                            </ul>
                        </div>
                        
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <form action="{{ route('warden.manage-hostel.store-rooms-details', $hostel) }}" method="POST">
                            @csrf
                            <input type="hidden" name="room_type_id" value="{{ $pendingAddRooms['room_type_id'] }}">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Room #</th>
                                            <th>Room Number <span class="text-danger">*</span></th>
                                            <th>Floor Number <span class="text-danger">*</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for($i = 0; $i < $pendingAddRooms['number_of_rooms']; $i++)
                                        <tr>
                                            <td class="align-middle">
                                                <span class="badge badge-primary">{{ $i + 1 }}</span>
                                            </td>
                                            <td>
                                                <input type="text" name="rooms[{{ $i }}][room_number]" 
                                                       class="form-control" placeholder="e.g. 101, A1, etc." required>
                                            </td>
                                            <td>
                                                <input type="text" name="rooms[{{ $i }}][floor_number]" 
                                                       class="form-control" placeholder="e.g. 1, 2, Ground, etc." required>
                                            </td>
                                        </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="alert alert-warning">
                                <h6 class="font-weight-bold"><i class="fas fa-exclamation-triangle"></i> Important Notes:</h6>
                                <ul class="mb-0">
                                    <li>Room numbers must be unique for this room type</li>
                                    <li>You can use any format: numbers (101, 102), letters (A1, A2), or combinations</li>
                                    <li>Floor numbers can be numbers or text (1, 2, Ground, First, etc.)</li>
                                    <li>All rooms will be set as "Available" status</li>
                                </ul>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-save"></i> Save Rooms
                                </button>
                                <a href="{{ route('warden.rooms.show', $hostel) }}" class="btn btn-secondary btn-lg ml-2">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
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
                        <div class="d-flex align-items-center">
                            <span class="text-muted mr-3">
                                Capacity: {{ $roomTypeData['type']->capacity ?? 'N/A' }} beds | 
                                Rent: ₹{{ $roomTypeData['type']->price_per_month ?? 'N/A' }}/month
                            </span>
                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#addRoomModal{{ $roomTypeData['type']->id ?? 'default' }}">
                                <i class="fas fa-plus"></i> Add Room
                            </button>
                            <button type="button" class="btn btn-primary btn-sm ml-2" data-toggle="modal" data-target="#bulkAddRoomModal{{ $roomTypeData['type']->id ?? 'default' }}">
                                <i class="fas fa-layer-group"></i> Bulk Add Rooms
                            </button>
                        </div>
                    </div>
                    
                    <!-- Bulk Delete Controls -->
                    <div class="card-body border-bottom bg-light" id="bulkControls{{ $roomTypeData['type']->id ?? 'default' }}" style="display: none;">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll{{ $roomTypeData['type']->id ?? 'default' }}">
                                    <label class="form-check-label" for="selectAll{{ $roomTypeData['type']->id ?? 'default' }}">
                                        <strong>Select All Rooms</strong>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                                            <button type="button" class="btn btn-danger btn-sm bulk-delete-btn" 
                                    data-room-type="{{ $roomTypeData['type']->id ?? 'default' }}"
                                    data-hostel-id="{{ $hostel->id }}">
                                <i class="fas fa-trash"></i> Delete Selected Rooms
                            </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($roomTypeData['rooms'] as $room)
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
                                    <div class="card border-{{ $room['color'] }} h-100 position-relative">
                                        <!-- Checkbox for bulk selection -->
                                        <div class="position-absolute" style="top: 5px; left: 5px;">
                                            <div class="form-check">
                                                <input class="form-check-input room-checkbox" type="checkbox" 
                                                       value="{{ $room['id'] ?? 0 }}" 
                                                       data-room-type="{{ $roomTypeData['type']->id ?? 'default' }}"
                                                       id="room{{ $room['id'] ?? 0 }}">
                                                <label class="form-check-label" for="room{{ $room['id'] ?? 0 }}"></label>
                                            </div>
                                        </div>
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
                                            <!-- Individual Room Actions -->
                                            <div class="mt-2">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#editRoomModal{{ $room['id'] }}" title="Edit Room">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    @if($room['occupants'] == 0)
                                                        <form action="{{ route('warden.hostels.rooms.destroy', [$hostel->id, $room['id']]) }}" method="POST" style="display:inline;" class="delete-room-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm delete-room-btn" data-room-number="{{ $room['room_number'] }}" title="Delete Room">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button type="button" class="btn btn-outline-secondary btn-sm" disabled title="Cannot delete room with occupants">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
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
                        <div class="col-md-2">
                            <div class="text-center">
                                <div class="h4 text-success">{{ $emptyRooms }}</div>
                                <div class="text-muted">Empty Rooms</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <div class="h4 text-info">{{ $emptyRooms }}</div>
                                <div class="text-muted">Available Rooms</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <div class="h4 text-warning">{{ $partialRooms }}</div>
                                <div class="text-muted">Partially Filled</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <div class="h4 text-danger">{{ $fullRooms }}</div>
                                <div class="text-muted">Fully Occupied</div>
                            </div>
                        </div>
                        <div class="col-md-2">
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

<!-- Bulk Add Room Modals -->
@if($roomsByType->count() > 0)
    @foreach($roomsByType as $roomTypeData)
        <div class="modal fade" id="bulkAddRoomModal{{ $roomTypeData['type']->id ?? 'default' }}" tabindex="-1" role="dialog" aria-labelledby="bulkAddRoomModalLabel{{ $roomTypeData['type']->id ?? 'default' }}" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form method="POST" action="{{ route('warden.rooms.bulkStore') }}" id="bulkRoomForm{{ $roomTypeData['type']->id ?? 'default' }}">
                        @csrf
                        <input type="hidden" name="hostel_id" value="{{ $hostel->id }}">
                        <input type="hidden" name="selected_room_type_id" value="{{ $roomTypeData['type']->id ?? '' }}">
                        
                        <div class="modal-header">
                            <h5 class="modal-title" id="bulkAddRoomModalLabel{{ $roomTypeData['type']->id ?? 'default' }}">
                                <i class="fas fa-layer-group"></i> Bulk Add Rooms - {{ $roomTypeData['type']->type ?? 'Unknown Type' }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        
                        <div class="modal-body">
                            <!-- Room Type Information -->
                            <div class="alert alert-info">
                                <h6 class="font-weight-bold"><i class="fas fa-info-circle"></i> Room Type Information:</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Type:</strong> {{ $roomTypeData['type']->type ?? 'Unknown' }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Capacity:</strong> {{ $roomTypeData['type']->capacity ?? 'N/A' }} students
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Price:</strong> ₹{{ number_format($roomTypeData['type']->price_per_month ?? 0, 2) }}/month
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Number of Rooms Input -->
                            <div class="form-group">
                                <label for="number_of_rooms_{{ $roomTypeData['type']->id ?? 'default' }}" class="font-weight-bold">
                                    <i class="fas fa-hashtag"></i> Number of Rooms to Add
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control form-control-lg" 
                                           id="number_of_rooms_{{ $roomTypeData['type']->id ?? 'default' }}" 
                                           name="number_of_rooms" min="1" max="50" value="1" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">rooms</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> You can add up to 50 rooms at once
                                </small>
                            </div>
                            
                            <!-- Room Details Section -->
                            <div id="roomDetailsSection{{ $roomTypeData['type']->id ?? 'default' }}" style="display: none;">
                                <hr>
                                <h6 class="font-weight-bold text-primary">
                                    <i class="fas fa-list"></i> Room Details
                                </h6>
                                <div id="roomDetailsFields{{ $roomTypeData['type']->id ?? 'default' }}">
                                    <!-- JS will generate fields here -->
                                </div>
                                
                                <div class="alert alert-warning mt-3">
                                    <h6 class="font-weight-bold"><i class="fas fa-exclamation-triangle"></i> Important Notes:</h6>
                                    <ul class="mb-0">
                                        <li>Room numbers must be unique for this room type</li>
                                        <li>You can use any format: numbers (101, 102), letters (A1, A2), or combinations</li>
                                        <li>Floor numbers can be numbers or text (1, 2, Ground, First, etc.)</li>
                                        <li>All rooms will be set as "Available" status</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success" id="bulkSubmitBtn{{ $roomTypeData['type']->id ?? 'default' }}">
                                <i class="fas fa-plus"></i> Add Rooms
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif

@include('components.student-profile-modal')

<!-- Edit Room Modals -->
@if($roomsByType->count() > 0)
    @foreach($roomsByType as $roomTypeData)
        @foreach($roomTypeData['rooms'] as $room)
            <div class="modal fade" id="editRoomModal{{ $room['id'] }}" tabindex="-1" role="dialog" aria-labelledby="editRoomModalLabel{{ $room['id'] }}" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form action="{{ route('warden.rooms.update', [$hostel->id, $room['id']]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title" id="editRoomModalLabel{{ $room['id'] }}">
                                    Edit Room {{ $room['room_number'] }}
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-info">
                                    <h6 class="font-weight-bold">Room Information:</h6>
                                    <ul class="mb-0">
                                        <li><strong>Type:</strong> {{ $roomTypeData['type']->type ?? 'Unknown' }}</li>
                                        <li><strong>Capacity:</strong> {{ $roomTypeData['type']->capacity ?? 'N/A' }} students</li>
                                        <li><strong>Current Occupants:</strong> {{ $room['occupants'] }}</li>
                                    </ul>
                                </div>
                                
                                <div class="form-group">
                                    <label for="room_number_edit_{{ $room['id'] }}">Room Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="room_number_edit_{{ $room['id'] }}" name="room_number" value="{{ $room['room_number'] }}" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="floor_edit_{{ $room['id'] }}">Floor Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="floor_edit_{{ $room['id'] }}" name="floor" value="{{ $room['floor'] }}" required>
                                </div>
                                
                                <input type="hidden" name="room_type_id" value="{{ $roomTypeData['type']->id ?? '' }}">
                                <input type="hidden" name="status" value="available">
                                <input type="hidden" name="current_occupants" value="{{ $room['occupants'] }}">
                                <input type="hidden" name="max_occupants" value="{{ $room['max_occupants'] }}">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endforeach
@endif

<!-- Add Room Modals -->
@if($roomsByType->count() > 0)
    @foreach($roomsByType as $roomTypeData)
        <div class="modal fade" id="addRoomModal{{ $roomTypeData['type']->id ?? 'default' }}" tabindex="-1" role="dialog" aria-labelledby="addRoomModalLabel{{ $roomTypeData['type']->id ?? 'default' }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST" action="{{ route('warden.manage-hostel.rooms.single.store', $hostel) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="addRoomModalLabel{{ $roomTypeData['type']->id ?? 'default' }}">
                                Add Room - {{ $roomTypeData['type']->type ?? 'Unknown Type' }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <h6 class="font-weight-bold">Room Type Information:</h6>
                                <ul class="mb-0">
                                    <li><strong>Type:</strong> {{ $roomTypeData['type']->type ?? 'Unknown' }}</li>
                                    <li><strong>Capacity:</strong> {{ $roomTypeData['type']->capacity ?? 'N/A' }} students</li>
                                    <li><strong>Price:</strong> ₹{{ number_format($roomTypeData['type']->price_per_month ?? 0, 2) }}/month</li>
                                </ul>
                            </div>
                            
                            <div class="form-group">
                                <label for="room_number_{{ $roomTypeData['type']->id ?? 'default' }}">Room Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="room_number_{{ $roomTypeData['type']->id ?? 'default' }}" name="room_number" placeholder="e.g. 101, A1, etc." required>
                                <small class="form-text text-muted">Enter a unique room number for this room type</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="floor_{{ $roomTypeData['type']->id ?? 'default' }}">Floor Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="floor_{{ $roomTypeData['type']->id ?? 'default' }}" name="floor" placeholder="e.g. 1, 2, Ground, etc." required>
                                <small class="form-text text-muted">Enter the floor number or name</small>
                            </div>
                            
                            <input type="hidden" name="room_type_id" value="{{ $roomTypeData['type']->id ?? '' }}">
                            <input type="hidden" name="status" value="available">
                            <input type="hidden" name="current_occupants" value="0">
                            <input type="hidden" name="max_occupants" value="{{ $roomTypeData['type']->capacity ?? 1 }}">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-plus"></i> Add Room
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle form submission for adding rooms
    const addRoomForms = document.querySelectorAll('form[action*="rooms/single"]');
    
    addRoomForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
            submitBtn.disabled = true;
            
            // Form will submit normally, but we've provided visual feedback
        });
    });
    
    // Bulk selection functionality
    const roomCheckboxes = document.querySelectorAll('.room-checkbox');
    const selectAllCheckboxes = document.querySelectorAll('[id^="selectAll"]');
    const bulkControls = document.querySelectorAll('[id^="bulkControls"]');
    const bulkDeleteBtns = document.querySelectorAll('.bulk-delete-btn');
    
    // Handle individual room checkbox changes
    roomCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const roomType = this.getAttribute('data-room-type');
            updateBulkControls(roomType);
            updateSelectAllCheckbox(roomType);
        });
    });
    
    // Handle select all checkbox changes
    selectAllCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const roomType = this.id.replace('selectAll', '');
            const roomTypeCheckboxes = document.querySelectorAll(`.room-checkbox[data-room-type="${roomType}"]`);
            
            roomTypeCheckboxes.forEach(roomCheckbox => {
                roomCheckbox.checked = this.checked;
            });
            
            updateBulkControls(roomType);
        });
    });
    
    // Handle bulk delete button clicks
    bulkDeleteBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const roomType = this.getAttribute('data-room-type');
            const hostelId = this.getAttribute('data-hostel-id');
            const selectedRooms = getSelectedRooms(roomType);
            
            console.log('Selected rooms:', selectedRooms);
            console.log('Room type:', roomType);
            console.log('Hostel ID:', hostelId);
            
            if (selectedRooms.length === 0) {
                alert('Please select at least one room to delete.');
                return;
            }
            
                    // Show simple confirmation dialog
            console.log('Showing bulk delete confirmation...');
            if (confirm(`Are you sure you want to delete ${selectedRooms.length} selected room(s)? This action cannot be undone.`)) {
                console.log('User confirmed bulk deletion, proceeding...');
                deleteSelectedRooms(selectedRooms, hostelId);
            } else {
                console.log('User cancelled bulk deletion');
            }
        });
    });
    
    // Individual room delete buttons now use simple confirmation
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, setting up delete confirmations...');
        
        // Add confirmation to individual delete buttons
        const deleteForms = document.querySelectorAll('.delete-room-form');
        console.log('Found delete forms:', deleteForms.length);
        
        deleteForms.forEach((form, index) => {
            console.log(`Setting up form ${index}:`, form);
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Delete form submitted, showing confirmation...');
                
                const roomNumber = this.querySelector('.delete-room-btn').getAttribute('data-room-number');
                console.log('Room number to delete:', roomNumber);
                
                if (confirm(`Are you sure you want to delete Room ${roomNumber}? This action cannot be undone.`)) {
                    console.log('User confirmed deletion, submitting form...');
                    this.submit();
                } else {
                    console.log('User cancelled deletion');
                }
            });
        });
    });
    
    // Function to update bulk controls visibility
    function updateBulkControls(roomType) {
        const roomTypeCheckboxes = document.querySelectorAll(`.room-checkbox[data-room-type="${roomType}"]`);
        const bulkControl = document.getElementById(`bulkControls${roomType}`);
        const selectedCount = Array.from(roomTypeCheckboxes).filter(cb => cb.checked).length;
        
        if (selectedCount > 0) {
            bulkControl.style.display = 'block';
        } else {
            bulkControl.style.display = 'none';
        }
    }
    
    // Function to update select all checkbox
    function updateSelectAllCheckbox(roomType) {
        const roomTypeCheckboxes = document.querySelectorAll(`.room-checkbox[data-room-type="${roomType}"]`);
        const selectAllCheckbox = document.getElementById(`selectAll${roomType}`);
        const checkedCount = Array.from(roomTypeCheckboxes).filter(cb => cb.checked).length;
        const totalCount = roomTypeCheckboxes.length;
        
        if (checkedCount === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (checkedCount === totalCount) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
            selectAllCheckbox.checked = false;
        }
    }
    
    // Function to get selected room IDs
    function getSelectedRooms(roomType) {
        const roomTypeCheckboxes = document.querySelectorAll(`.room-checkbox[data-room-type="${roomType}"]:checked`);
        return Array.from(roomTypeCheckboxes).map(cb => cb.value);
    }
    
    // Function to delete selected rooms
    function deleteSelectedRooms(roomIds, hostelId) {
        console.log('Deleting rooms:', roomIds);
        console.log('Hostel ID:', hostelId);
        
        // Create a form to submit the delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/warden/hostels/${hostelId}/rooms/bulk-delete`;
        
        console.log('Form action:', form.action);
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (csrfMeta) {
            csrfToken.value = csrfMeta.getAttribute('content');
        } else {
            // Fallback: get CSRF token from any existing form
            const existingForm = document.querySelector('form');
            if (existingForm) {
                const existingToken = existingForm.querySelector('input[name="_token"]');
                if (existingToken) {
                    csrfToken.value = existingToken.value;
                }
            }
        }
        form.appendChild(csrfToken);
        

        
        // Add room IDs
        roomIds.forEach(roomId => {
            const roomIdField = document.createElement('input');
            roomIdField.type = 'hidden';
            roomIdField.name = 'room_ids[]';
            roomIdField.value = roomId;
            form.appendChild(roomIdField);
        });
        
        console.log('Form data:', {
            method: form.method,
            action: form.action,
            roomIds: roomIds,
            csrfToken: csrfToken.value
        });
        
        // Show loading state
        const deleteBtn = document.querySelector('.bulk-delete-btn[data-hostel-id="' + hostelId + '"]');
        if (deleteBtn) {
            const originalText = deleteBtn.innerHTML;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
            deleteBtn.disabled = true;
        }
        
        // Submit the form
        document.body.appendChild(form);
        console.log('Form element:', form);
        console.log('Form HTML:', form.outerHTML);
        form.submit();
    }
    
    // Show success message if present
    @if(session('success'))
        // You can add custom success handling here if needed
        console.log('Room added successfully');
    @endif
    
            // Show error messages if present
        @if($errors->any())
            // You can add custom error handling here if needed
            console.log('There were errors in the form');
        @endif
    
    // Bulk Add Room Modal Functionality
    @if($roomsByType->count() > 0)
        @foreach($roomsByType as $roomTypeData)
            const roomTypeId{{ $roomTypeData['type']->id ?? 'default' }} = '{{ $roomTypeData['type']->id ?? 'default' }}';
            const roomTypeName{{ $roomTypeData['type']->id ?? 'default' }} = '{{ $roomTypeData['type']->type ?? 'Unknown Type' }}';
            
            // Function to render room details for this room type
            function renderBulkRoomDetails{{ $roomTypeData['type']->id ?? 'default' }}() {
                const numberOfRooms = parseInt(document.getElementById('number_of_rooms_{{ $roomTypeData['type']->id ?? 'default' }}').value) || 0;
                const roomDetailsSection = document.getElementById('roomDetailsSection{{ $roomTypeData['type']->id ?? 'default' }}');
                const roomDetailsFields = document.getElementById('roomDetailsFields{{ $roomTypeData['type']->id ?? 'default' }}');
                
                if (numberOfRooms > 0) {
                    roomDetailsSection.style.display = 'block';
                    
                    let html = `
                        <div class="alert alert-info">
                            <h6 class="font-weight-bold"><i class="fas fa-info-circle"></i> Room Type: ${roomTypeName{{ $roomTypeData['type']->id ?? 'default' }}}</h6>
                            <p class="mb-0">You're adding ${numberOfRooms} room${numberOfRooms > 1 ? 's' : ''} of type "${roomTypeName{{ $roomTypeData['type']->id ?? 'default' }}}"</p>
                        </div>
                    `;
                    
                    for (let i = 0; i < numberOfRooms; i++) {
                        html += `
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-hashtag"></i> Room Number ${i + 1} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="rooms[0][${i}][room_number]" class="form-control" 
                                           placeholder="e.g. 101, A1, etc." required>
                                    <small class="form-text text-muted">Must be unique</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-building"></i> Floor Number ${i + 1} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="rooms[0][${i}][floor]" class="form-control" 
                                           placeholder="e.g. 1, 2, Ground, etc." required>
                                    <small class="form-text text-muted">Can be number or text</small>
                                </div>
                                <input type="hidden" name="rooms[0][${i}][type_id]" value="${roomTypeId{{ $roomTypeData['type']->id ?? 'default' }}}">
                            </div>
                        `;
                    }
                    
                    roomDetailsFields.innerHTML = html;
                } else {
                    roomDetailsSection.style.display = 'none';
                }
            }
            
            // Event listener for number of rooms input
            document.getElementById('number_of_rooms_{{ $roomTypeData['type']->id ?? 'default' }}').addEventListener('input', renderBulkRoomDetails{{ $roomTypeData['type']->id ?? 'default' }});
            
            // Handle form submission for bulk add
            document.getElementById('bulkRoomForm{{ $roomTypeData['type']->id ?? 'default' }}').addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('bulkSubmitBtn{{ $roomTypeData['type']->id ?? 'default' }}');
                const originalText = submitBtn.innerHTML;
                
                // Show loading state
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding Rooms...';
                submitBtn.disabled = true;
                
                // Form will submit normally, but we've provided visual feedback
            });
            
            // Initialize room details when modal opens
            $('#bulkAddRoomModal{{ $roomTypeData['type']->id ?? 'default' }}').on('shown.bs.modal', function () {
                renderBulkRoomDetails{{ $roomTypeData['type']->id ?? 'default' }}();
            });
        @endforeach
    @endif
    });
</script>
@endpush
@endsection 