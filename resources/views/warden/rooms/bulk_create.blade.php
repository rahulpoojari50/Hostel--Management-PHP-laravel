@extends('layouts.admin')

@section('title', 'Bulk Add Rooms')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Bulk Add Rooms – {{ $hostel->name }}</h1>
    @if($roomTypes->isEmpty())
        <div class="alert alert-warning">
            No room types found for this hostel. Please <a href="{{ route('warden.hostels.room-types.index', $hostel) }}">add room types</a> first.
        </div>
    @else
    <form method="POST" action="{{ route('warden.rooms.bulkStore') }}">
        @csrf
        <input type="hidden" name="hostel_id" value="{{ $hostel->id }}">

        <!-- Step 1: Select Room Types and Counts -->
        <div class="card mb-4">
            <div class="card-header fw-bold">Step 1: Select Room Types and Counts</div>
            <div class="card-body">
                <div class="mb-3">
                    <label>How many room types do you want to add?</label>
                    <select id="roomTypeCount" class="form-select" style="width:auto; display:inline-block;">
                        @for($i=1; $i<=5; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div id="roomTypeInputs"></div>
            </div>
        </div>

        <!-- Step 2: Room Details (auto-generated) -->
        <div class="card mb-4">
            <div class="card-header fw-bold">Step 2: Enter Room Details</div>
            <div class="card-body" id="roomDetailsSection">
                <!-- JS will generate fields here -->
            </div>
        </div>

        <button type="submit" class="btn btn-success">Add Rooms</button>
    </form>
    @endif
</div>

<script>
const roomTypes = @json($roomTypes);

function renderRoomTypeInputs(count) {
    let html = '';
    for (let i = 0; i < count; i++) {
        html += `
            <div class="row mb-2 align-items-end">
                <div class="col-md-5">
                    <label>Room Type</label>
                    <select name="room_types[${i}][type_id]" class="form-select" required>
                        <option value="">Select Type</option>
                        ${roomTypes.map(rt => `<option value=\"${rt.id}\">${rt.type}</option>`).join('')}
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Number of Rooms</label>
                    <input type="number" name="room_types[${i}][count]" class="form-control" min="1" required>
                </div>
            </div>
        `;
    }
    document.getElementById('roomTypeInputs').innerHTML = html;
    document.getElementById('roomDetailsSection').innerHTML = '';
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

    let html = '';
    roomTypesData.forEach((rt, i) => {
        if (!rt.type_id || !rt.count) return;
        const typeName = roomTypes.find(t => t.id == rt.type_id)?.type || '';
        html += `<h5 class=\"mt-3\">Room Type: ${typeName} (${rt.count} Rooms)</h5><hr>`;
        for (let j = 0; j < rt.count; j++) {
            html += `
                <div class=\"row mb-2\">
                    <div class=\"col-md-4\">
                        <label>Room No</label>
                        <input type=\"text\" name=\"rooms[${i}][${j}][room_number]\" class=\"form-control\" required>
                    </div>
                    <div class=\"col-md-4\">
                        <label>Floor No</label>
                        <input type=\"number\" name=\"rooms[${i}][${j}][floor]\" class=\"form-control\" required>
                    </div>
                    <input type=\"hidden\" name=\"rooms[${i}][${j}][type_id]\" value=\"${rt.type_id}\">
                </div>
            `;
        }
    });
    document.getElementById('roomDetailsSection').innerHTML = html;
}

document.getElementById('roomTypeCount').addEventListener('change', function() {
    renderRoomTypeInputs(this.value);
});
document.getElementById('roomTypeInputs').addEventListener('change', renderRoomDetails);

// Initial render
renderRoomTypeInputs(document.getElementById('roomTypeCount').value);
</script>
@endsection 