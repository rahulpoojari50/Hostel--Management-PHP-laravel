@extends('layouts.admin')

@section('title', 'Manage ' . $hostel->name)

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manage {{ $hostel->name }}</h1>
    <div>
        <a href="{{ route('warden.manage-hostel.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Hostels
        </a>
      
        <button type="button" class="btn btn-primary btn-sm ml-2" data-toggle="modal" data-target="#addRoomsModal">
            <i class="fas fa-bed"></i> Add Rooms
        </button>
        <a href="{{ route('warden.hostels.rooms.bulkCreate', $hostel) }}" class="btn btn-success btn-sm ml-2">
            <i class="fas fa-layer-group"></i> Bulk Add Rooms
        </a>
    </div>
</div>

<!-- Add Room Type Modal -->
<div class="modal fade" id="addRoomTypeModal" tabindex="-1" role="dialog" aria-labelledby="addRoomTypeModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" action="{{ route('warden.hostels.room-types.store', $hostel) }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="addRoomTypeModalLabel">Add Custom Room Type</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- Predefined Room Types Status -->
          <div class="alert alert-info">
            <h6 class="font-weight-bold">Predefined Room Types Status:</h6>
            @php
                $existingTypes = $hostel->roomTypes()->pluck('type')->toArray();
                $predefinedTypes = ['Single Sharing', 'Double Sharing', 'Triple Sharing', 'Four Sharing'];
            @endphp
            <div class="row">
                @foreach($predefinedTypes as $type)
                    <div class="col-6">
                        @if(in_array($type, $existingTypes))
                            <span class="badge badge-success"><i class="fas fa-check"></i> {{ $type }}</span>
                        @else
                            <span class="badge badge-secondary"><i class="fas fa-times"></i> {{ $type }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
            <small class="text-muted mt-2 d-block">Predefined room types are automatically created. You can only add custom room types here.</small>
          </div>
          
          <div class="form-group">
            <label for="roomType">Custom Room Type Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="customRoomType" name="type" placeholder="e.g. Deluxe Single, Premium Double, Executive Suite, etc." required>
            <small class="form-text text-muted">Enter a unique name for your custom room type</small>
          </div>
          
          <div class="form-group">
            <label for="capacity">Capacity (Number of Students per Room) <span class="text-danger">*</span></label>
            <input type="number" class="form-control" id="capacity" name="capacity" min="1" max="20" required>
          </div>
          
          <div class="form-group">
            <label for="price_per_month">Price per Month (₹) <span class="text-danger">*</span></label>
            <input type="number" class="form-control" id="price_per_month" name="price_per_month" min="0" step="0.01" required>
          </div>
          
          <div class="form-group">
            <label for="total_rooms">Total Rooms <span class="text-danger">*</span></label>
            <input type="number" class="form-control" id="total_rooms" name="total_rooms" min="1" required>
          </div>
          
          <div class="form-group">
            <label for="facilities">Facilities (comma separated)</label>
            <input type="text" class="form-control" id="facilities" name="facilities" placeholder="AC, WiFi, Attached Bathroom, etc.">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Custom Room Type</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add Rooms Modal -->
<div class="modal fade" id="addRoomsModal" tabindex="-1" role="dialog" aria-labelledby="addRoomsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form method="POST" action="{{ route('warden.manage-hostel.add-rooms', $hostel) }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="addRoomsModalLabel">Add Rooms to Existing Room Type</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="room_type_id">Select Room Type <span class="text-danger">*</span></label>
            <select class="form-control" id="room_type_id" name="room_type_id" required>
              <option value="">Choose a room type...</option>
              @foreach($hostel->roomTypes as $roomType)
                <option value="{{ $roomType->id }}" data-capacity="{{ $roomType->capacity }}" data-price="{{ $roomType->price_per_month }}">
                  {{ $roomType->type }} (Capacity: {{ $roomType->capacity }}, Price: ₹{{ number_format($roomType->price_per_month, 2) }}/month)
                </option>
              @endforeach
            </select>
            <small class="form-text text-muted">Select the room type you want to add rooms to</small>
          </div>
          
          <div class="form-group">
            <label for="number_of_rooms">Number of Rooms to Add <span class="text-danger">*</span></label>
            <input type="number" class="form-control" id="number_of_rooms" name="number_of_rooms" min="1" max="10" required>
            <small class="form-text text-muted">Enter how many rooms you want to add to this room type (max 10 at once)</small>
          </div>
          
          <div class="alert alert-info">
            <h6 class="font-weight-bold"><i class="fas fa-info-circle"></i> Room Information:</h6>
            <ul class="mb-0">
                                       <li>Each room will have the same capacity and price as the selected room type</li>
                         <li>You will be prompted to enter floor and room numbers for each room</li>
                         <li>All new rooms will be set as "Available" status</li>
                         <li>You can edit room details later from the rooms management section</li>
            </ul>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Rooms</button>
        </div>
      </form>
    </div>
  </div>
</div>

@include('components.breadcrumb', [
    'pageTitle' => $pageTitle,
    'breadcrumbs' => $breadcrumbs
])

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

<!-- Management Sections -->
<div class="row">
    <!-- Add Room Types (Step 2) -->
    <div class="col-lg-12 mb-4">
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
                                <a href="{{ route('warden.manage-hostel.show', $hostel) }}" class="btn btn-secondary btn-lg ml-2">
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

<!-- Room Management Section -->
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bed"></i> Room Management
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-left-primary">
                            <div class="card-body">
                                <h6 class="font-weight-bold text-primary">Add Rooms to Existing Room Types</h6>
                                <p class="text-muted">Add more rooms to room types you've already created.</p>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addRoomsModal">
                                    <i class="fas fa-plus"></i> Add Rooms
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-left-success">
                            <div class="card-body">
                                <h6 class="font-weight-bold text-success">Room Statistics</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h4 class="text-primary">{{ $hostel->rooms->count() }}</h4>
                                            <small class="text-muted">Total Rooms</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h4 class="text-success">{{ $hostel->rooms->where('status', 'available')->count() }}</h4>
                                            <small class="text-muted">Available Rooms</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Update Meals Menu -->
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Update Meals Menu</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('warden.manage-hostel.menu.update', $hostel) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="menu">Weekly Menu</label>
                        
                        <!-- Desktop/Tablet View -->
                        <div class="d-none d-md-block">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm meals-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="min-width: 80px;">Day</th>
                                            <th style="min-width: 120px;">Breakfast</th>
                                            <th style="min-width: 120px;">Lunch</th>
                                            <th style="min-width: 120px;">Snacks</th>
                                            <th style="min-width: 120px;">Dinner</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
                                            $mealTypes = ['breakfast','lunch','snacks','dinner'];
                                            $menu = $hostel->menu ?? [];
                                        @endphp
                                        @foreach($days as $day)
                                        <tr>
                                            <td class="align-middle"><strong>{{ $day }}</strong></td>
                                            @foreach($mealTypes as $meal)
                                            <td>
                                                <input type="text" class="form-control form-control-sm" name="menu[{{ $day }}][{{ $meal }}]" value="{{ $menu[$day][$meal] ?? '' }}" placeholder="-">
                                            </td>
                                            @endforeach
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Mobile View -->
                        <div class="d-md-none">
                            @foreach($days as $day)
                            <div class="card mb-3">
                                <div class="card-header py-2">
                                    <h6 class="mb-0 font-weight-bold">{{ $day }}</h6>
                                </div>
                                <div class="card-body py-2">
                                    @foreach($mealTypes as $meal)
                                    <div class="form-group mb-2">
                                        <label class="small mb-1">{{ ucfirst($meal) }}</label>
                                        <input type="text" class="form-control form-control-sm" name="menu[{{ $day }}][{{ $meal }}]" value="{{ $menu[$day][$meal] ?? '' }}" placeholder="-">
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <small class="form-text text-muted">Fill in the menu for each meal and day. Use short descriptions for better fit.</small>
                    </div>
                    <button type="submit" class="btn btn-info btn-sm">
                        <i class="fas fa-utensils fa-sm"></i> Update Menu
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Facilities -->
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Add/Update Facilities</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('warden.manage-hostel.facilities.update', $hostel) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="description">Facilities Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" 
                                  placeholder="Describe the facilities available in this hostel...">{{ $hostel->description ?? '' }}</textarea>
                        <small class="form-text text-muted">List all available facilities like WiFi, Laundry, etc.</small>
                    </div>
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-tools fa-sm"></i> Update Facilities
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Room Types & Rooms Summary Table -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Current Room Types & Availability</h6>
            </div>
            <div class="card-body">
                @if($hostel->roomTypes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Room Type</th>
                                    <th>Capacity</th>
                                    <th>Rent/month</th>
                                    <th>Total Rooms</th>
                                    <th>Rooms Added</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hostel->roomTypes as $roomType)
                                    @php
                                        $rooms = $hostel->rooms->where('room_type_id', $roomType->id);
                                    @endphp
                                    <tr>
                                        <td>{{ $roomType->type }}</td>
                                        <td>{{ $roomType->capacity }}</td>
                                        <td>₹{{ $roomType->price_per_month }}</td>
                                        <td>{{ $rooms->count() }}</td>
                                        <td>
                                            @foreach($rooms as $room)
                                                <span class="badge badge-light mr-1">
                                                    {{ $room->room_number }}
                                                    <!-- Edit Room Modal Trigger -->
                                                    <a href="#" data-toggle="modal" data-target="#editRoomModal{{ $room->id }}" title="Edit"><i class="fas fa-edit text-primary ml-1"></i></a>
                                                    <!-- Delete Room Form -->
                                                    <form action="{{ route('warden.hostels.rooms.destroy', [$hostel->id, $room->id]) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline" title="Delete"><i class="fas fa-trash text-danger ml-1"></i></button>
                                                    </form>
                                                </span>
                                                <!-- Edit Room Modal -->
                                                <div class="modal fade" id="editRoomModal{{ $room->id }}" tabindex="-1" role="dialog" aria-labelledby="editRoomModalLabel{{ $room->id }}" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <form action="{{ route('warden.rooms.update', [$hostel->id, $room->id]) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="editRoomModalLabel{{ $room->id }}">Edit Room {{ $room->room_number }}</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <label>Room Number</label>
                                                                        <input type="text" name="room_number" class="form-control" value="{{ $room->room_number }}" required>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Floor</label>
                                                                        <input type="text" name="floor" class="form-control" value="{{ $room->floor }}" required>
                                                                    </div>
                                                                    <input type="hidden" name="room_type_id" value="{{ $roomType->id }}">
                                                                    <input type="hidden" name="status" value="{{ $room->status }}">
                                                                    <input type="hidden" name="current_occupants" value="{{ $room->current_occupants }}">
                                                                    <input type="hidden" name="max_occupants" value="{{ $room->max_occupants }}">
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </td>
                                        <td>
                                            <!-- Edit Room Type Modal Trigger -->
                                            <a href="#" data-toggle="modal" data-target="#editRoomTypeModal{{ $roomType->id }}" class="btn btn-sm btn-info" title="Edit"><i class="fas fa-edit"></i></a>
                                            <!-- Delete Room Type Form -->
                                            <form action="{{ route('warden.hostels.room-types.destroy', [$hostel->id, $roomType->id]) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete room type {{ $roomType->type }}?')"><i class="fas fa-trash"></i></button>
                                            </form>
                                            <!-- Edit Room Type Modal -->
                                            <div class="modal fade" id="editRoomTypeModal{{ $roomType->id }}" tabindex="-1" role="dialog" aria-labelledby="editRoomTypeModalLabel{{ $roomType->id }}" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="{{ route('warden.hostels.room-types.update', [$hostel, $roomType]) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editRoomTypeModalLabel{{ $roomType->id }}">Edit Room Type</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Type</label>
                                                                    <select name="type" class="form-control" required>
                                                                        <option value="Single" {{ $roomType->type == 'Single' ? 'selected' : '' }}>Single</option>
                                                                        <option value="Double" {{ $roomType->type == 'Double' ? 'selected' : '' }}>Double</option>
                                                                        <option value="Triple" {{ $roomType->type == 'Triple' ? 'selected' : '' }}>Triple</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Capacity</label>
                                                                    <input type="number" name="capacity" class="form-control" value="{{ $roomType->capacity }}" readonly required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Rent</label>
                                                                    <input type="number" name="price_per_month" class="form-control" value="{{ $roomType->price_per_month }}" min="0" step="0.01" required>
                                                                </div>
                                                                <input type="hidden" name="total_rooms" value="{{ $roomType->total_rooms }}">
                                                                <input type="hidden" name="available_rooms" value="0">
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted">
                        <i class="fas fa-bed fa-2x mb-2"></i>
                        <p>No room types defined yet. Add room types above to get started.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 
@push('scripts')
<style>
    /* Custom styles for meals menu */
    .meals-table {
        font-size: 0.875rem;
    }
    
    .meals-table th,
    .meals-table td {
        padding: 0.5rem;
        vertical-align: middle;
    }
    
    .meals-table input {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .meals-table {
            font-size: 0.75rem;
        }
        
        .meals-table th,
        .meals-table td {
            padding: 0.25rem;
        }
        
        .meals-table input {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }
    }
    
    @media (max-width: 576px) {
        .meals-table {
            font-size: 0.7rem;
        }
        
        .meals-table th,
        .meals-table td {
            padding: 0.2rem;
        }
        
        .meals-table input {
            font-size: 0.65rem;
            padding: 0.15rem 0.3rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let feeIndex = 0;
        // Remove default fee row
        document.querySelectorAll('.remove-default-fee-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const fee = btn.getAttribute('data-fee');
                const row = document.getElementById('fee-row-' + fee.replace('_', '-')) || document.getElementById('fee-row-' + fee);
                if (row) row.remove();
            });
        });
        document.getElementById('add-fee-btn').addEventListener('click', function(e) {
            e.preventDefault();
            addOptionalFeeRow();
        });
        function addOptionalFeeRow() {
            feeIndex++;
            const section = document.getElementById('optional-fees-section');
            const row = document.createElement('div');
            row.className = 'form-row align-items-end mb-2';
            row.innerHTML = `
                <div class="col-md-6">
                    <input type="text" class="form-control" name="optional_fees[${feeIndex}][type]" placeholder="Fee Name" required>
                </div>
                <div class="col-md-4">
                    <input type="number" class="form-control" name="optional_fees[${feeIndex}][amount]" min="0" step="0.01" placeholder="Amount">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-fee-btn"><i class="fas fa-trash"></i></button>
                </div>
            `;
            section.appendChild(row);
            row.querySelector('.remove-fee-btn').addEventListener('click', function() {
                row.remove();
            });
        }

        // Add Rooms Modal functionality
        const roomTypeSelect = document.getElementById('room_type_id');
        const numberOfRoomsInput = document.getElementById('number_of_rooms');
        
        if (roomTypeSelect) {
            roomTypeSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    const capacity = selectedOption.getAttribute('data-capacity');
                    const price = selectedOption.getAttribute('data-price');
                    
                    // Update the info alert with selected room type details
                    const infoAlert = document.querySelector('#addRoomsModal .alert-info ul');
                    if (infoAlert) {
                        infoAlert.innerHTML = `
                            <li>Room Type: <strong>${selectedOption.text.split(' (')[0]}</strong></li>
                            <li>Capacity: <strong>${capacity} students</strong></li>
                            <li>Price: <strong>₹${parseFloat(price).toLocaleString('en-IN', {minimumFractionDigits: 2})}/month</strong></li>
                            <li>Each room will have the same capacity and price as the selected room type</li>
                            <li>Room numbers and floor numbers will be assigned automatically</li>
                            <li>All new rooms will be set as "Available" status</li>
                            <li>You can edit room details later from the rooms management section</li>
                        `;
                    }
                }
            });
        }

        // Validate number of rooms input
        if (numberOfRoomsInput) {
            numberOfRoomsInput.addEventListener('input', function() {
                const value = parseInt(this.value);
                if (value > 10) {
                    this.value = 10;
                    alert('Maximum 10 rooms can be added at once for better control.');
                }
            });
        }

        // Form validation for room details
        const roomForm = document.querySelector('form[action*="store-rooms-details"]');
        if (roomForm) {
            roomForm.addEventListener('submit', function(e) {
                const roomInputs = this.querySelectorAll('input[name*="[room_number]"]');
                const floorInputs = this.querySelectorAll('input[name*="[floor_number]"]');
                const roomNumbers = [];
                const floorNumbers = [];
                
                // Collect all room and floor numbers
                roomInputs.forEach((input, index) => {
                    const roomNumber = input.value.trim();
                    const floorNumber = floorInputs[index].value.trim();
                    
                    if (roomNumber && floorNumber) {
                        const key = `${roomNumber}-${floorNumber}`;
                        if (roomNumbers.includes(key)) {
                            e.preventDefault();
                            alert(`Duplicate room found: Room ${roomNumber} on floor ${floorNumber}. Please use unique room numbers.`);
                            return;
                        }
                        roomNumbers.push(key);
                    }
                });
                
                // Check for empty fields
                let hasEmptyFields = false;
                roomInputs.forEach((input, index) => {
                    if (!input.value.trim() || !floorInputs[index].value.trim()) {
                        hasEmptyFields = true;
                    }
                });
                
                if (hasEmptyFields) {
                    e.preventDefault();
                    alert('Please fill in all room numbers and floor numbers.');
                    return;
                }
            });
        }
    });
</script>
@endpush 