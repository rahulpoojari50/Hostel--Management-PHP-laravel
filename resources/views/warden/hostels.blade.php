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