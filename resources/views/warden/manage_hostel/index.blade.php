@extends('layouts.admin')

@section('title', 'Manage Hostel')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <!-- Breadcrumb Navigation -->
        @include('components.breadcrumb-nav', ['breadcrumbs' => $breadcrumbs])
    </div>
    <div>
        <a href="{{ route('warden.hostels.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add Hostel
        </a>
    </div>
</div>

<!-- Page Title -->
<div class="mb-4">
    <h5 class="mb-0 text-gray-800">Manage Hostel</h5>
</div>

<!-- Content Row -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Select a Hostel to Manage</h6>
            </div>
            <div class="card-body">
                @if($hostels->count() > 0)
                    <div class="row">
                        @foreach($hostels as $hostel)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card border-left-primary shadow h-100">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    {{ $hostel->name }}
                                                </div>
                                                <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                    {{ ucfirst($hostel->type) }} Hostel
                                                </div>
                                                <div class="text-muted small">
                                                    {{ Str::limit($hostel->address, 50) }}
                                                </div>
                                                <div class="mt-3">
                                                    <a href="{{ route('warden.manage-hostel.show', $hostel) }}" 
                                                       class="btn btn-primary btn-sm">
                                                        <i class="fas fa-cogs fa-sm"></i> Manage
                                                    </a>
                                                    <button type="button" class="btn btn-danger btn-sm ml-2" 
                                                            onclick="confirmDeleteHostel({{ $hostel->id }}, '{{ $hostel->name }}')">
                                                        <i class="fas fa-trash fa-sm"></i> Delete
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-building fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted">
                        <i class="fas fa-building fa-3x mb-3"></i>
                        <h5>No Hostels Available</h5>
                        <p>You don't have any hostels assigned to manage.</p>
                        <a href="{{ route('warden.hostels.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus fa-sm"></i> Create Hostel
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- Delete Hostel Form (Hidden) -->
<form id="deleteHostelForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Delete Confirmation Modal -->
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

<script>
function confirmDeleteHostel(hostelId, hostelName) {
    document.getElementById('hostelNameToDelete').textContent = hostelName;
    document.getElementById('deleteHostelForm').action = '{{ route("warden.hostels.destroy", ":hostelId") }}'.replace(':hostelId', hostelId);
    $('#deleteHostelModal').modal('show');
}

function deleteHostel() {
    document.getElementById('deleteHostelForm').submit();
}
</script>

@endsection 