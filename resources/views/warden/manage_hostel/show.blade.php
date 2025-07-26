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
        <button type="button" class="btn btn-success btn-sm ml-2" data-toggle="modal" data-target="#addRoomTypeModal">
            <i class="fas fa-plus"></i> Add Room Type
        </button>
    </div>
</div>

<!-- Add Room Type Modal -->
<div class="modal fade" id="addRoomTypeModal" tabindex="-1" role="dialog" aria-labelledby="addRoomTypeModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" action="{{ route('warden.hostels.room-types.store', $hostel) }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="addRoomTypeModalLabel">Add Room Type</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="roomType">Type</label>
            <input type="text" class="form-control" id="roomType" name="type" placeholder="e.g. Single, Double, Triple, Quad" required>
          </div>
          <div class="form-group">
            <label for="capacity">Capacity</label>
            <input type="number" class="form-control" id="capacity" name="capacity" min="1" max="20" required>
          </div>
          <div class="form-group">
            <label for="price_per_month">Price per Month</label>
            <input type="number" class="form-control" id="price_per_month" name="price_per_month" required>
          </div>
          <div class="form-group">
            <label for="total_rooms">Total Rooms</label>
            <input type="number" class="form-control" id="total_rooms" name="total_rooms" required>
          </div>
          <div class="form-group">
            <label for="available_rooms">Available Rooms</label>
            <input type="number" class="form-control" id="available_rooms" name="available_rooms" required>
          </div>
          <div class="form-group">
            <label for="facilities">Facilities (comma separated)</label>
            <input type="text" class="form-control" id="facilities" name="facilities" placeholder="AC, WiFi, etc.">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Room Type</button>
        </div>
      </form>
    </div>
  </div>
</div>

@include('components.breadcrumb', [
    'pageTitle' => 'Manage Hostel',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'Manage Hostel', 'url' => route('warden.manage-hostel.index')],
        ['name' => $hostel->name, 'url' => '']
    ]
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
            </div>
        </div>
    </div>
</div>

<!-- Update Rent -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Update Rent</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('warden.manage-hostel.rent.update', $hostel) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="rent_room_type_id">Room Type</label>
                        <select class="form-control" id="rent_room_type_id" name="room_type_id" required>
                            <option value="">Select Room Type</option>
                            @foreach($hostel->roomTypes as $roomType)
                                <option value="{{ $roomType->id }}">
                                    {{ $roomType->type }} - Current: ${{ $roomType->price_per_month }}/month
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="new_price_per_month">New Rent</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" class="form-control" id="new_price_per_month" name="price_per_month" 
                                   min="0" step="0.01" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-dollar-sign fa-sm"></i> Update Rent
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Fees Section -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Add Fees</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('warden.manage-hostel.fees.update', $hostel) }}" method="POST">
                    @csrf
                    <!-- Default Fees (now removable) -->
                    <div id="default-fees-section">
                        <div class="form-group form-row align-items-end mb-2" id="fee-row-admission_fee">
                            <div class="col-md-6">
                                <label for="admission_fee">Admission Fee</label>
                                <input type="number" class="form-control" id="admission_fee" name="fees[admission_fee]" value="1" min="0" step="0.01" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm remove-default-fee-btn" data-fee="admission_fee"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                        <div class="form-group form-row align-items-end mb-2" id="fee-row-seat_rent">
                            <div class="col-md-6">
                                <label for="seat_rent">Security Fees</label>
                                <input type="number" class="form-control" id="seat_rent" name="fees[seat_rent]" value="1" min="0" step="0.01" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm remove-default-fee-btn" data-fee="seat_rent"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                        <div class="form-group form-row align-items-end mb-2" id="fee-row-medical_aid_fee">
                            <div class="col-md-6">
                                <label for="medical_aid_fee">Medical Aid Fee</label>
                                <input type="number" class="form-control" id="medical_aid_fee" name="fees[medical_aid_fee]" value="1" min="0" step="0.01" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm remove-default-fee-btn" data-fee="medical_aid_fee"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                        <div class="form-group form-row align-items-end mb-2" id="fee-row-mess_fee">
                            <div class="col-md-6">
                                <label for="mess_fee">Mess Fee</label>
                                <input type="number" class="form-control" id="mess_fee" name="fees[mess_fee]" value="1" min="0" step="0.01" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm remove-default-fee-btn" data-fee="mess_fee"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                    <!-- Dynamic Optional Fees -->
                    <div id="optional-fees-section"></div>
                    <button type="button" class="btn btn-link p-0 mb-3" id="add-fee-btn">
                        <i class="fas fa-plus"></i> Add Another Fee
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus fa-sm"></i> Add/Update Fees
                    </button>
                </form>
            </div>
        </div>
    </div>
    <!-- End Add Fees Section -->

    <!-- Update Meals Menu -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Update Meals Menu</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('warden.manage-hostel.menu.update', $hostel) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="menu">Weekly Menu</label>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Day</th>
                                        <th>Breakfast</th>
                                        <th>Lunch</th>
                                        <th>Snacks</th>
                                        <th>Dinner</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
                                        $mealTypes = ['breakfast','lunch','snacks','dinner'];
                                        $menu = is_array($hostel->menu) ? $hostel->menu : (json_decode($hostel->menu, true) ?? []);
                                    @endphp
                                    @foreach($days as $day)
                                    <tr>
                                        <td><strong>{{ $day }}</strong></td>
                                        @foreach($mealTypes as $meal)
                                        <td>
                                            <input type="text" class="form-control" name="menu[{{ $day }}][{{ $meal }}]" value="{{ $menu[$day][$meal] ?? '' }}" placeholder="-">
                                        </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <small class="form-text text-muted">Fill in the menu for each meal and day.</small>
                    </div>
                    <button type="submit" class="btn btn-info">
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
                                                    <form action="{{ route('warden.warden.rooms.destroy', [$hostel->id, $room->id]) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline" onclick="return confirm('Delete room {{ $room->room_number }}?')" title="Delete"><i class="fas fa-trash text-danger ml-1"></i></button>
                                                    </form>
                                                </span>
                                                <!-- Edit Room Modal -->
                                                <div class="modal fade" id="editRoomModal{{ $room->id }}" tabindex="-1" role="dialog" aria-labelledby="editRoomModalLabel{{ $room->id }}" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <form action="{{ route('warden.warden.rooms.update', [$hostel->id, $room->id]) }}" method="POST">
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
                                                        <form action="{{ route('warden.room-types.update', $roomType->id) }}" method="POST">
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
    });
</script>
@endpush 