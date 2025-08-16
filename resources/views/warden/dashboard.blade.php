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
        <a href="{{ $hostels->count() > 0 ? route('warden.hostels.students', $hostels->first()->id) : '#' }}" class="text-decoration-none">
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
                            <a href="{{ route('warden.hostel-attendance.hostels') }}" class="text-decoration-none">
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
                                            <a href="{{ route('warden.hostel-attendance.index', $hostel) }}" 
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
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
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

<!-- Simple Delete Confirmation Modal -->
<div class="modal fade" id="deleteHostelModal" tabindex="-1" role="dialog" aria-labelledby="deleteHostelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteHostelModalLabel">Confirm Hostel Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the hostel <strong id="hostelNameToDelete"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning:</strong> This action cannot be undone. All associated data (rooms, room types, applications, etc.) will be permanently deleted.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="deleteHostel()">Delete Hostel</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Hostel Form (Hidden) -->
<form id="deleteHostelForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

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
            
            document.getElementById('hostelNameToDelete').textContent = hostelName;
            document.getElementById('deleteHostelForm').action = '{{ route("warden.hostels.destroy", ":hostelId") }}'.replace(':hostelId', hostelId);
            
            $('#deleteHostelModal').modal('show');
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

    // Hostel deletion function
    function deleteHostel() {
        document.getElementById('deleteHostelForm').submit();
    }
</script>
@endpush
