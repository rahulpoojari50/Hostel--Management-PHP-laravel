@extends('layouts.admin')

@section('title', 'Bulk Add Rooms')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-layer-group text-primary"></i> Bulk Add Rooms
            </h1>
            <p class="text-muted mb-0">{{ $hostel->name }}</p>
        </div>
        <div>
            <a href="{{ route('warden.manage-hostel.show', $hostel) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Hostel
            </a>
        </div>
    </div>

    @if($roomTypes->isEmpty())
        <!-- No Room Types Warning -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-white">
                        <i class="fas fa-exclamation-triangle"></i> No Room Types Found
                    </div>
                    <div class="card-body text-center py-5">
                        <i class="fas fa-bed fa-3x text-warning mb-3"></i>
                        <h4 class="text-gray-800">No room types available</h4>
                        <p class="text-muted">You need to add room types before you can create rooms.</p>
                        <a href="{{ route('warden.hostels.room-types.index', $hostel) }}" class="btn btn-warning">
                            <i class="fas fa-plus"></i> Add Room Types
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Main Form -->
        <form method="POST" action="{{ route('warden.rooms.bulkStore') }}" id="bulkRoomForm">
            @csrf
            <input type="hidden" name="hostel_id" value="{{ $hostel->id }}">

            @if($selectedRoomType)
                <!-- Single Room Type Bulk Add -->
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Room Type Information Card -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-bed"></i> {{ $selectedRoomType->type }} - Room Details
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Room Type Info -->
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <div class="text-center p-3 bg-light rounded">
                                            <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                            <h6 class="mb-1">Capacity</h6>
                                            <span class="badge badge-primary">{{ $selectedRoomType->capacity ?? 'N/A' }} students</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center p-3 bg-light rounded">
                                            <i class="fas fa-rupee-sign fa-2x text-success mb-2"></i>
                                            <h6 class="mb-1">Monthly Rent</h6>
                                            <span class="badge badge-success">₹{{ number_format($selectedRoomType->price_per_month ?? 0, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center p-3 bg-light rounded">
                                            <i class="fas fa-info-circle fa-2x text-info mb-2"></i>
                                            <h6 class="mb-1">Room Type</h6>
                                            <span class="badge badge-info">{{ $selectedRoomType->type }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Number of Rooms Input -->
                                <div class="form-group">
                                    <label for="number_of_rooms" class="font-weight-bold">
                                        <i class="fas fa-hashtag"></i> Number of Rooms to Add
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control form-control-lg" id="number_of_rooms" 
                                               name="number_of_rooms" min="1" max="50" value="1" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">rooms</span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> You can add up to 50 rooms at once
                                    </small>
                                </div>
                                
                                <input type="hidden" name="selected_room_type_id" value="{{ $selectedRoomType->id }}">
                            </div>
                        </div>

                        <!-- Room Details Section -->
                        <div class="card shadow-sm mb-4" id="roomDetailsCard" style="display: none;">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-list"></i> Room Details
                                </h5>
                            </div>
                            <div class="card-body" id="roomDetailsSection">
                                <!-- JS will generate fields here -->
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Progress Card -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-tasks"></i> Progress</h6>
                            </div>
                            <div class="card-body">
                                <div class="progress mb-3" style="height: 10px;">
                                    <div class="progress-bar" id="progressBar" role="progressbar" style="width: 0%"></div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Step 1</span>
                                    <span class="text-muted">Step 2</span>
                                </div>
                                <div class="mt-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-check-circle text-success mr-2" id="step1Check"></i>
                                        <span>Room Type Selected</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-circle text-muted mr-2" id="step2Check"></i>
                                        <span>Room Details</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tips Card -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-lightbulb"></i> Tips</h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success mr-2"></i>
                                        Use unique room numbers
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success mr-2"></i>
                                        Floor numbers can be text or numbers
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success mr-2"></i>
                                        All rooms will be set as "Available"
                                    </li>
                                    <li>
                                        <i class="fas fa-check text-success mr-2"></i>
                                        You can edit rooms later
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Multiple Room Types Bulk Add -->
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Step 1: Room Types Selection -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-list-ol"></i> Step 1: Select Room Types and Counts
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-question-circle"></i> How many room types do you want to add?
                                    </label>
                                    <select id="roomTypeCount" class="form-control form-control-lg" style="width: auto;">
                                        @for($i=1; $i<=5; $i++)
                                            <option value="{{ $i }}">{{ $i }} room type{{ $i > 1 ? 's' : '' }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div id="roomTypeInputs"></div>
                            </div>
                        </div>

                        <!-- Step 2: Room Details -->
                        <div class="card shadow-sm mb-4" id="roomDetailsCard" style="display: none;">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-edit"></i> Step 2: Enter Room Details
                                </h5>
                            </div>
                            <div class="card-body" id="roomDetailsSection">
                                <!-- JS will generate fields here -->
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Progress Card -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-tasks"></i> Progress</h6>
                            </div>
                            <div class="card-body">
                                <div class="progress mb-3" style="height: 10px;">
                                    <div class="progress-bar" id="progressBar" role="progressbar" style="width: 50%"></div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Step 1</span>
                                    <span class="text-muted">Step 2</span>
                                </div>
                                <div class="mt-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-check-circle text-success mr-2" id="step1Check"></i>
                                        <span>Room Types Selected</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-circle text-muted mr-2" id="step2Check"></i>
                                        <span>Room Details</span>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            @endif

            <!-- Submit Button -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                                <i class="fas fa-plus"></i> Add Rooms
                            </button>
                            <a href="{{ route('warden.manage-hostel.show', $hostel) }}" class="btn btn-outline-secondary btn-lg ml-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>

<script>
const roomTypes = @json($roomTypes);
const selectedRoomType = @json($selectedRoomType);

@if($selectedRoomType)
// Single room type bulk add functionality
function renderSingleRoomTypeDetails() {
    const numberOfRooms = parseInt(document.getElementById('number_of_rooms').value) || 0;
    const roomTypeId = {{ $selectedRoomType->id }};
    const roomTypeName = '{{ $selectedRoomType->type }}';
    
    if (numberOfRooms > 0) {
        document.getElementById('roomDetailsCard').style.display = 'block';
        document.getElementById('step2Check').className = 'fas fa-check-circle text-success mr-2';
        document.getElementById('progressBar').style.width = '100%';
        
        let html = `
            <div class="alert alert-info">
                <h6 class="font-weight-bold"><i class="fas fa-info-circle"></i> Room Type: ${roomTypeName}</h6>
                <p class="mb-0">You're adding ${numberOfRooms} room${numberOfRooms > 1 ? 's' : ''} of type "${roomTypeName}"</p>
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
                    <input type="hidden" name="rooms[0][${i}][type_id]" value="${roomTypeId}">
                </div>
            `;
        }
        
        html += `
            <div class="alert alert-warning">
                <h6 class="font-weight-bold"><i class="fas fa-exclamation-triangle"></i> Important Notes:</h6>
                <ul class="mb-0">
                    <li>Room numbers must be unique for this room type</li>
                    <li>You can use any format: numbers (101, 102), letters (A1, A2), or combinations</li>
                    <li>Floor numbers can be numbers or text (1, 2, Ground, First, etc.)</li>
                    <li>All rooms will be set as "Available" status</li>
                </ul>
            </div>
        `;
        
        document.getElementById('roomDetailsSection').innerHTML = html;
    } else {
        document.getElementById('roomDetailsCard').style.display = 'none';
        document.getElementById('step2Check').className = 'fas fa-circle text-muted mr-2';
        document.getElementById('progressBar').style.width = '50%';
    }
}

// Event listeners for single room type
document.getElementById('number_of_rooms').addEventListener('input', renderSingleRoomTypeDetails);

// Initial render for single room type
renderSingleRoomTypeDetails();
@else
// Multiple room types bulk add functionality
function renderRoomTypeInputs(count) {
    let html = '';
    for (let i = 0; i < count; i++) {
        html += `
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="card-title text-primary">Room Type ${i + 1}</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="font-weight-bold">Room Type</label>
                            <select name="room_types[${i}][type_id]" class="form-control" required>
                                <option value="">Select Type</option>
                                ${roomTypes.map(rt => `<option value="${rt.id}">${rt.type} (${rt.capacity} beds)</option>`).join('')}
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="font-weight-bold">Number of Rooms</label>
                            <input type="number" name="room_types[${i}][count]" class="form-control" min="1" max="20" placeholder="1-20" required>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    document.getElementById('roomTypeInputs').innerHTML = html;
    document.getElementById('roomDetailsSection').innerHTML = '';
    document.getElementById('roomDetailsCard').style.display = 'none';
    document.getElementById('step2Check').className = 'fas fa-circle text-muted mr-2';
    document.getElementById('progressBar').style.width = '50%';
}

function renderRoomDetails() {
    const roomTypesData = [];
    document.querySelectorAll('[name^="room_types"]').forEach(input => {
        const match = input.name.match(/room_types\[(\d+)\]\[(type_id|count)\]/);
        if (match) {
            const idx = match[1];
            if (!roomTypesData[idx]) roomTypesData[idx] = {};
            roomTypesData[idx][match[2]] = input.value;
        }
    });

    let hasValidData = false;
    roomTypesData.forEach(rt => {
        if (rt.type_id && rt.count) hasValidData = true;
    });

    if (hasValidData) {
        document.getElementById('roomDetailsCard').style.display = 'block';
        document.getElementById('step2Check').className = 'fas fa-check-circle text-success mr-2';
        document.getElementById('progressBar').style.width = '100%';
    } else {
        document.getElementById('roomDetailsCard').style.display = 'none';
        document.getElementById('step2Check').className = 'fas fa-circle text-muted mr-2';
        document.getElementById('progressBar').style.width = '50%';
    }

    let html = '';
    roomTypesData.forEach((rt, i) => {
        if (!rt.type_id || !rt.count) return;
        const typeName = roomTypes.find(t => t.id == rt.type_id)?.type || '';
        const typeCapacity = roomTypes.find(t => t.id == rt.type_id)?.capacity || '';
        
        html += `
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-bed"></i> ${typeName} (${rt.count} rooms, ${typeCapacity} beds each)
                    </h6>
                </div>
                <div class="card-body">
        `;
        
        for (let j = 0; j < rt.count; j++) {
            html += `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="font-weight-bold">Room Number ${j + 1}</label>
                        <input type="text" name="rooms[${i}][${j}][room_number]" class="form-control" 
                               placeholder="e.g. 101, A1, etc." required>
                    </div>
                    <div class="col-md-6">
                        <label class="font-weight-bold">Floor Number ${j + 1}</label>
                        <input type="text" name="rooms[${i}][${j}][floor]" class="form-control" 
                               placeholder="e.g. 1, 2, Ground, etc." required>
                    </div>
                    <input type="hidden" name="rooms[${i}][${j}][type_id]" value="${rt.type_id}">
                </div>
            `;
        }
        
        html += `
                </div>
            </div>
        `;
    });
    
    document.getElementById('roomDetailsSection').innerHTML = html;
}

// Event listeners for multiple room types
document.getElementById('roomTypeCount').addEventListener('change', function() {
    renderRoomTypeInputs(this.value);
});

document.getElementById('roomTypeInputs').addEventListener('change', renderRoomDetails);

// Initial render for multiple room types
renderRoomTypeInputs(document.getElementById('roomTypeCount').value);
@endif

// Form validation
document.getElementById('bulkRoomForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding Rooms...';
});
</script>
@endsection 