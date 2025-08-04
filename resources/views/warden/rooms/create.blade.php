@extends('layouts.admin')

@section('title', 'Add Room - ' . $hostel->name)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add Room – {{ $hostel->name }}</h1>
        <div>
            <a href="{{ route('warden.rooms.show', $hostel) }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Rooms
            </a>
        </div>
    </div>

    @if($roomTypes->isEmpty())
        <div class="alert alert-warning">
            No room types found for this hostel. Please <a href="{{ route('warden.hostels.room-types.index', $hostel) }}">add room types</a> first.
        </div>
    @else
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Add New Room</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('warden.hostels.rooms.store', $hostel) }}">
                @csrf
                <input type="hidden" name="hostel_id" value="{{ $hostel->id }}">

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="room_number" class="form-label">Room Number *</label>
                            <input type="text" class="form-control @error('room_number') is-invalid @enderror" 
                                   id="room_number" name="room_number" value="{{ old('room_number') }}" required>
                            @error('room_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="room_type_id" class="form-label">Room Type *</label>
                            <select class="form-select @error('room_type_id') is-invalid @enderror" 
                                    id="room_type_id" name="room_type_id" required>
                                <option value="">Select Room Type</option>
                                @foreach($roomTypes as $roomType)
                                    <option value="{{ $roomType->id }}" {{ old('room_type_id') == $roomType->id ? 'selected' : '' }}>
                                        {{ ucfirst($roomType->type) }} (Capacity: {{ $roomType->capacity }})
                                    </option>
                                @endforeach
                            </select>
                            @error('room_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="floor" class="form-label">Floor *</label>
                            <input type="number" class="form-control @error('floor') is-invalid @enderror" 
                                   id="floor" name="floor" value="{{ old('floor', 1) }}" min="1" required>
                            @error('floor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="current_occupants" class="form-label">Current Occupants</label>
                            <input type="number" class="form-control @error('current_occupants') is-invalid @enderror" 
                                   id="current_occupants" name="current_occupants" value="{{ old('current_occupants', 0) }}" min="0" required>
                            @error('current_occupants')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="max_occupants" class="form-label">Max Occupants</label>
                            <input type="number" class="form-control @error('max_occupants') is-invalid @enderror" 
                                   id="max_occupants" name="max_occupants" value="{{ old('max_occupants', 1) }}" min="1" required>
                            @error('max_occupants')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('warden.rooms.show', $hostel) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Add Room</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

<script>
// Auto-fill max_occupants based on room type selection
document.getElementById('room_type_id').addEventListener('change', function() {
    const roomTypeId = this.value;
    const roomTypes = @json($roomTypes);
    const selectedType = roomTypes.find(type => type.id == roomTypeId);
    
    if (selectedType) {
        document.getElementById('max_occupants').value = selectedType.capacity;
    }
});
</script>
@endsection 