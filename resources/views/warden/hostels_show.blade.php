@extends('layouts.admin')

@section('title', 'Hostel Details')

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Hostel Details – {{ $hostel->name }}</h1>
        <div>
            <a href="#students-list" class="btn btn-info btn-sm mr-2"><i class="fas fa-users"></i> View Students</a>
            <button type="button" class="btn btn-danger btn-sm delete-hostel-btn" 
                    data-hostel-id="{{ $hostel->id }}" 
                    data-hostel-name="{{ $hostel->name }}">
                <i class="fas fa-trash"></i> Delete Hostel
            </button>
        </div>
    </div>

    @include('components.breadcrumb', [
        'pageTitle' => 'Hostel Details',
        'breadcrumbs' => [
            ['name' => 'Home', 'url' => url('/')],
            ['name' => 'Hostels Management', 'url' => route('warden.hostels.index')],
            ['name' => 'Hostel Details', 'url' => '']
        ]
    ])
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">General Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-6"><strong>Name:</strong> {{ $hostel->name }}</div>
                        <div class="col-md-6"><strong>Type:</strong> {{ ucfirst($hostel->type) }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6"><strong>Status:</strong> {{ ucfirst($hostel->status) }}</div>
                        <div class="col-md-6"><strong>Warden:</strong> {{ $hostel->warden->name ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-12"><strong>Address:</strong> {{ $hostel->address }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-12"><strong>Description:</strong> {{ $hostel->description }}</div>
                    </div>
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Room Types & Occupancy</h6>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Type</th><th>Capacity</th><th>Rent/month</th><th>Total Rooms</th><th>Occupied</th><th>Vacant</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hostel->roomTypes as $type)
                                @php
                                    $totalRooms = $type->rooms->count();
                                    $occupied = $type->rooms->filter(fn($r) => $r->status === 'occupied')->count();
                                    $vacant = $totalRooms - $occupied;
                                @endphp
                                <tr>
                                    <td>{{ $type->type }}</td>
                                    <td>{{ $type->capacity }}</td>
                                    <td>₹{{ $type->price_per_month }}</td>
                                    <td>{{ $totalRooms }}</td>
                                    <td>{{ $occupied }}</td>
                                    <td>{{ $vacant }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card shadow mb-4" id="students-list">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Students Allotted Rooms</h6>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th><th>USN</th><th>Email</th><th>Room Type</th><th>Room No</th><th>Floor No</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                @php
                                    $assignment = $student->roomAssignments->where('room.hostel_id', $hostel->id)->first();
                                @endphp
                                <tr>
                                    <td>
                                        <a href="#" class="student-name-clickable text-primary" data-student-id="{{ $student->id }}" style="text-decoration: none; cursor: pointer;">
                                            <i class="fas fa-user mr-1"></i>{{ $student->name }}
                                        </a>
                                    </td>
                                    <td>{{ $student->usn ?? '-' }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>{{ $assignment->room->roomType->type ?? '-' }}</td>
                                    <td>{{ $assignment->room->room_number ?? '-' }}</td>
                                    <td>{{ $assignment->room->floor ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">No students allotted rooms.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                {{ $students->links('pagination::bootstrap-4') }}
            </div>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Meal Menu</h6>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="thead-light">
                            <tr><th>Day</th><th>Breakfast</th><th>Lunch</th><th>Snacks</th><th>Dinner</th></tr>
                        </thead>
                        <tbody>
                            @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                                <tr>
                                    <td class="font-weight-bold">{{ $day }}</td>
                                    @foreach(['breakfast','lunch','snacks','dinner'] as $meal)
                                        <td>{{ $hostel->menu[$day][$meal] ?? '-' }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Facilities</h6>
                </div>
                <div class="card-body">
                    {!! nl2br(e($hostel->description)) !!}
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Room List</h6>
                </div>
                <div class="card-body table-responsive">
                    <table class="table mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Room No.</th><th>Type</th><th>Status</th><th>Occupants</th><th>Max</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hostel->rooms as $room)
                                <tr>
                                    <td>{{ $room->room_number }}</td>
                                    <td>{{ $room->roomType->type ?? '-' }}</td>
                                    <td class="text-capitalize">{{ $room->status }}</td>
                                    <td>{{ $room->current_occupants }}</td>
                                    <td>{{ $room->max_occupants }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center">No rooms found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Three-Step Delete Confirmation Modal -->
<div class="modal fade" id="deleteHostelModal" tabindex="-1" role="dialog" aria-labelledby="deleteHostelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteHostelModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> Delete Hostel - Step 1 of 3
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Step 1: Initial Warning -->
                <div id="step1" class="delete-step">
                    <div class="alert alert-danger">
                        <h6 class="font-weight-bold"><i class="fas fa-exclamation-triangle"></i> WARNING: You are about to delete a hostel!</h6>
                        <p class="mb-0">This action will:</p>
                        <ul class="mb-0">
                            <li>Remove the hostel from active management</li>
                            <li>Hide it from the main hostel list</li>
                            <li>Keep all data for potential restoration</li>
                        </ul>
                    </div>
                    <div class="text-center">
                        <h5>Are you sure you want to delete <strong id="hostelName1"></strong>?</h5>
                        <p class="text-muted">This is step 1 of 3. You will need to confirm two more times.</p>
                    </div>
                </div>

                <!-- Step 2: Data Impact Warning -->
                <div id="step2" class="delete-step" style="display: none;">
                    <div class="alert alert-warning">
                        <h6 class="font-weight-bold"><i class="fas fa-database"></i> Data Impact Warning</h6>
                        <p class="mb-0">Deleting this hostel will affect:</p>
                        <ul class="mb-0">
                            <li>All room applications and assignments</li>
                            <li>Student attendance records</li>
                            <li>Fee collection data</li>
                            <li>Meal attendance records</li>
                        </ul>
                    </div>
                    <div class="text-center">
                        <h5>Do you understand the data impact?</h5>
                        <p class="text-muted">This is step 2 of 3. One more confirmation required.</p>
                    </div>
                </div>

                <!-- Step 3: Final Confirmation -->
                <div id="step3" class="delete-step" style="display: none;">
                    <div class="alert alert-danger">
                        <h6 class="font-weight-bold"><i class="fas fa-trash"></i> Final Confirmation</h6>
                        <p class="mb-0">This is your final chance to cancel. After this step, the hostel will be deleted.</p>
                    </div>
                    <div class="text-center">
                        <h5>Type "DELETE" to confirm</h5>
                        <div class="form-group">
                            <input type="text" class="form-control" id="deleteConfirmation" placeholder="Type DELETE to confirm" style="text-align: center; font-weight: bold;">
                        </div>
                        <p class="text-muted">This is step 3 of 3. Type "DELETE" exactly to proceed.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="nextStepBtn">Next Step</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" style="display: none;">Delete Hostel</button>
            </div>
        </div>
    </div>
</div>

@include('components.student-profile-modal')
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Delete hostel button click
        $('.delete-hostel-btn').click(function() {
            const hostelId = $(this).data('hostel-id');
            const hostelName = $(this).data('hostel-name');
            
            $('#hostelName1').text(hostelName);
            $('#deleteHostelModal').data('hostel-id', hostelId);
            $('#deleteHostelModal').data('current-step', 1);
            
            // Reset modal state
            $('.delete-step').hide();
            $('#step1').show();
            $('#nextStepBtn').show();
            $('#confirmDeleteBtn').hide();
            $('#deleteConfirmation').val('');
            
            $('#deleteHostelModal').modal('show');
        });

        // Next step button
        $('#nextStepBtn').click(function() {
            const currentStep = $('#deleteHostelModal').data('current-step');
            const nextStep = currentStep + 1;
            
            if (nextStep <= 3) {
                $('.delete-step').hide();
                $(`#step${nextStep}`).show();
                $('#deleteHostelModal').data('current-step', nextStep);
                
                if (nextStep === 3) {
                    $(this).hide();
                    $('#confirmDeleteBtn').show();
                }
            }
        });

        // Final delete confirmation
        $('#confirmDeleteBtn').click(function() {
            const confirmation = $('#deleteConfirmation').val();
            if (confirmation === 'DELETE') {
                const hostelId = $('#deleteHostelModal').data('hostel-id');
                
                // Create and submit form
                const form = $('<form>', {
                    'method': 'POST',
                    'action': `/warden/hostels/${hostelId}`
                });
                
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': $('meta[name="csrf-token"]').attr('content')
                }));
                
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_method',
                    'value': 'DELETE'
                }));
                
                $('body').append(form);
                form.submit();
            } else {
                alert('Please type "DELETE" exactly to confirm.');
            }
        });
    });
</script>
@endpush
