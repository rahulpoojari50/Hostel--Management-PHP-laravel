@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    {{-- <a href="{{ route('warden.hostels.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Add Hostel
    </a> --}}
</div>

@include('components.breadcrumb', [
    'pageTitle' => 'Hostel Dashboard',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'Hostel Dashboard', 'url' => '']
    ]
])

<!-- Content Row -->
<div class="row">

    <!-- Total Hostels Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
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
    </div>

    <!-- Pending Applications Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
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
    </div>

    <!-- Total Students Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
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
    </div>

    <!-- Total Rooms Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
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
                                <tr>
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
                                            <a href="{{ route('warden.hostels.edit', $hostel) }}" 
                                               class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('warden.hostels.destroy', $hostel) }}" 
                                                  method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Are you sure you want to delete this hostel?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
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
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Applications</h6>
            </div>
            <div class="card-body">
                @if(isset($recentApplications) && $recentApplications->count() > 0)
                    @foreach($recentApplications as $application)
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <i class="fas fa-file-alt fa-2x text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ml-3">
                                <div class="font-weight-bold">{{ $application->student->name ?? 'Unknown Student' }}</div>
                                <div class="text-muted small">{{ $application->created_at->diffForHumans() }}</div>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="badge badge-{{ $application->status === 'pending' ? 'warning' : ($application->status === 'approved' ? 'success' : 'danger') }}">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </div>
                        </div>
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
                        <a href="{{ route('warden.meals.index') }}" class="btn btn-warning btn-block">
                            <i class="fas fa-utensils fa-sm"></i> Manage Meals
                        </a>
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
    });
</script>
@endpush
