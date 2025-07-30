@extends('layouts.admin')

@section('title', 'Hostels')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    {{-- <a href="{{ route('warden.hostels.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Add Hostel
    </a> --}}
</div>

@include('components.breadcrumb', [
    'pageTitle' => 'Hostels Management',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'Hostels Management', 'url' => '']
    ]
])

<!-- Content Row -->
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">All Hostels</h6>
                <a href="{{ route('warden.hostels.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus fa-sm"></i> Add Hostel
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Location</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hostels as $hostel)
                                <tr>
                                    <td>{{ $hostel->name }}</td>
                                    <td><span class="badge badge-info">{{ ucfirst($hostel->type) }}</span></td>
                                    <td>
                                        <span class="badge badge-{{ $hostel->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($hostel->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $hostel->location ?? 'N/A' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('warden.hostels.show', $hostel) }}" 
                                               class="btn btn-info btn-sm" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('warden.hostels.edit', $hostel) }}" 
                                               class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('warden.hostels.rooms.manage', $hostel) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-bed"></i> Manage Rooms
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm delete-hostel-btn" 
                                                    data-hostel-id="{{ $hostel->id }}" 
                                                    data-hostel-name="{{ $hostel->name }}"
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                        No hostels found. Start by creating one.
                                    </td>
                                </tr>
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

<!-- Restore Hostels Section -->
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-undo"></i> Deleted Hostels (Restore)
                </h6>
            </div>
            <div class="card-body">
                <div id="deletedHostelsList">
                    <!-- Deleted hostels will be loaded here via AJAX -->
                    <div class="text-center text-muted">
                        <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                        <p>Loading deleted hostels...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "order": [[ 0, "asc" ]],
            "pageLength": 10,
            "language": {
                "search": "Search hostels:",
                "lengthMenu": "Show _MENU_ hostels per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ hostels"
            }
        });

        // Load deleted hostels
        loadDeletedHostels();

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

        // Load deleted hostels function
        function loadDeletedHostels() {
            $.ajax({
                url: '{{ route("warden.hostels.deleted") }}',
                method: 'GET',
                success: function(response) {
                    if (response.hostels && response.hostels.length > 0) {
                        let html = '<div class="table-responsive"><table class="table table-bordered">';
                        html += '<thead><tr><th>Name</th><th>Type</th><th>Deleted Date</th><th>Actions</th></tr></thead><tbody>';
                        
                        response.hostels.forEach(function(hostel) {
                            html += `<tr>
                                <td>${hostel.name}</td>
                                <td><span class="badge badge-info">${hostel.type}</span></td>
                                <td>${hostel.deleted_at}</td>
                                <td>
                                    <button class="btn btn-success btn-sm restore-hostel-btn" data-hostel-id="${hostel.id}">
                                        <i class="fas fa-undo"></i> Restore
                                    </button>
                                </td>
                            </tr>`;
                        });
                        
                        html += '</tbody></table></div>';
                        $('#deletedHostelsList').html(html);
                    } else {
                        $('#deletedHostelsList').html('<div class="text-center text-muted"><i class="fas fa-check-circle fa-2x mb-2"></i><p>No deleted hostels found.</p></div>');
                    }
                },
                error: function() {
                    $('#deletedHostelsList').html('<div class="text-center text-danger"><i class="fas fa-exclamation-triangle fa-2x mb-2"></i><p>Error loading deleted hostels.</p></div>');
                }
            });
        }

        // Restore hostel button (delegated event)
        $(document).on('click', '.restore-hostel-btn', function() {
            const hostelId = $(this).data('hostel-id');
            
            if (confirm('Are you sure you want to restore this hostel?')) {
                $.ajax({
                    url: `/warden/hostels/${hostelId}/restore`,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Hostel restored successfully!');
                            location.reload();
                        } else {
                            alert('Error restoring hostel.');
                        }
                    },
                    error: function() {
                        alert('Error restoring hostel.');
                    }
                });
            }
        });
    });
</script>
@endpush 