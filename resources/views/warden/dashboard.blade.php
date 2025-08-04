@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <!-- Breadcrumb Navigation -->
        @include('components.breadcrumb-nav', ['breadcrumbs' => $breadcrumbs])
    </div>
    <div>
        {{-- <a href="{{ route('warden.hostels.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add Hostel
        </a> --}}
    </div>
</div>

<!-- Page Title -->
<div class="mb-4">
    <h5 class="mb-0 text-gray-800">Hostel Dashboard</h5>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Total Hostels Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('warden.hostels.index') }}" class="text-decoration-none">
            <div class="card border-left-primary shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Hostels</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalHostels }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Pending Applications Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('warden.applications.index') }}" class="text-decoration-none">
            <div class="card border-left-success shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Pending Applications</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingApplications }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Total Students Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('warden.room-allotment.index') }}" class="text-decoration-none">
            <div class="card border-left-info shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Students</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalStudents }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Total Rooms Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('warden.rooms.index') }}" class="text-decoration-none">
            <div class="card border-left-warning shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Rooms</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalRooms }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bed fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Additional Quick Stats -->
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('warden.fees.index') }}" class="text-decoration-none">
            <div class="card border-left-danger shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Fee Management</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Manage Fees</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('warden.hostels_attendance_hostels') }}" class="text-decoration-none">
            <div class="card border-left-secondary shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Hostel Attendance</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Track Attendance</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('warden.attendance.report') }}" class="text-decoration-none">
            <div class="card border-left-dark shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                Attendance Reports</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">View Reports</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('warden.meals-attendance.index') }}" class="text-decoration-none">
            <div class="card border-left-info shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Meal Attendance</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Track Meals</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Hostels Table -->
    <div class="col-lg-12 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Hostel Overview</h6>
                <a href="{{ route('warden.hostels.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus fa-sm"></i> Add Hostel
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Hostel Name</th>
                                <th>Description</th>
                                <th>Location</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hostels as $hostel)
                                <tr class="clickable-row" data-href="{{ route('warden.hostels.show', $hostel) }}">
                                    <td>{{ $hostel->name }}</td>
                                    <td>{{ Str::limit($hostel->description, 50) }}</td>
                                    <td>{{ $hostel->location ?? 'N/A' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('warden.hostels.show', $hostel) }}" 
                                               class="btn btn-info btn-sm" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('warden.hostels.students', $hostel) }}" 
                                               class="btn btn-primary btn-sm" title="View Students">
                                                <i class="fas fa-users"></i>
                                            </a>
                                            <a href="{{ route('warden.hostels.attendance', $hostel) }}" 
                                               class="btn btn-success btn-sm" title="Attendance">
                                                <i class="fas fa-clipboard-check"></i>
                                            </a>
                                            <a href="{{ route('warden.hostels.room-types.index', $hostel) }}" 
                                               class="btn btn-secondary btn-sm" title="View Room Types">
                                                <i class="fas fa-bed"></i>
                                            </a>
                                            <a href="{{ route('warden.hostels.edit', $hostel) }}" 
                                               class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
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
                                    <td colspan="4" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                        No hostels available. Start by creating one.
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

<!-- Content Row -->
<div class="row">

    <!-- Recent Applications -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Recent Applications</h6>
                <a href="{{ route('warden.applications.index') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-eye fa-sm"></i> View All
                </a>
            </div>
            <div class="card-body">
                @if(isset($recentApplications) && $recentApplications->count() > 0)
                    @foreach($recentApplications as $application)
                        <a href="{{ route('warden.applications.show', $application) }}" class="text-decoration-none">
                            <div class="d-flex align-items-center mb-3 recent-application-item">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-file-alt fa-2x text-primary"></i>
                                </div>
                                <div class="flex-grow-1 ml-3">
                                    <div class="font-weight-bold text-dark">{{ $application->student->name ?? 'Unknown Student' }}</div>
                                    <div class="text-muted small">{{ $application->created_at->diffForHumans() }}</div>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="badge badge-{{ $application->status === 'pending' ? 'warning' : ($application->status === 'approved' ? 'success' : 'danger') }}">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                @else
                    <div class="text-center text-muted">
                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
                        No recent applications
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                <a href="{{ route('warden.hostels.index') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-cogs fa-sm"></i> Manage All
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-3">
                        <a href="{{ route('warden.hostels.create') }}" class="btn btn-primary btn-block">
                            <i class="fas fa-plus fa-sm"></i> Add Hostel
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="{{ route('warden.applications.index') }}" class="btn btn-info btn-block">
                            <i class="fas fa-file-alt fa-sm"></i> View Applications
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="{{ route('warden.rooms.index') }}" class="btn btn-success btn-block">
                            <i class="fas fa-bed fa-sm"></i> Manage Rooms
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="{{ route('warden.selectHostel.manage') }}" class="btn btn-warning btn-block">
                            <i class="fas fa-utensils fa-sm"></i> Manage Meals
                        </a>
                    </div>

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

@endsection

@push('styles')
<style>
    .dashboard-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .dashboard-card:hover .text-gray-300 {
        color: #6c757d !important;
    }
    
    .dashboard-card:hover .text-primary {
        color: #007bff !important;
    }
    
    .dashboard-card:hover .text-success {
        color: #28a745 !important;
    }
    
    .dashboard-card:hover .text-info {
        color: #17a2b8 !important;
    }
    
    .dashboard-card:hover .text-warning {
        color: #ffc107 !important;
    }
    
    .dashboard-card:hover .text-danger {
        color: #dc3545 !important;
    }
    
    .dashboard-card:hover .text-secondary {
        color: #6c757d !important;
    }
    
    .dashboard-card:hover .text-dark {
        color: #343a40 !important;
    }
    
    .clickable-row {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    
    .clickable-row:hover {
        background-color: #f8f9fc !important;
    }
    
    .recent-application-item {
        transition: all 0.2s ease;
        border-radius: 0.35rem;
        padding: 0.75rem;
    }
    
    .recent-application-item:hover {
        background-color: #f8f9fc;
        transform: translateX(5px);
    }
</style>
@endpush

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

        // Clickable table rows
        $('.clickable-row').click(function(e) {
            // Don't trigger if clicking on buttons
            if ($(e.target).closest('.btn-group, .btn').length > 0) {
                return;
            }
            
            const href = $(this).data('href');
            if (href) {
                window.location.href = href;
            }
        });
    });
</script>
@endpush
