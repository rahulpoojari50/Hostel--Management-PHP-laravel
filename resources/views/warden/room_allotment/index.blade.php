@extends('layouts.admin')

@section('title', 'Room Allotment')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    {{-- <h1 class="h3 mb-0 text-gray-800">Room Allotment</h1> --}}
</div>

@include('components.breadcrumb', [
    'pageTitle' => 'Room Allotment',
    'breadcrumbs' => [
        ['name' => 'Hostel Dashboard', 'url' => url('/warden/dashboard')],
        ['name' => 'Room Allotment', 'url' => '']
    ]
])

<!-- Add Room Type Button and Modal -->
<div class="mb-3 d-flex justify-content-end">
    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#addRoomTypeModal">
        <i class="fas fa-plus"></i> Add Room Type
    </button>
</div>

<!-- Add Room Type Modal -->
<div class="modal fade" id="addRoomTypeModal" tabindex="-1" role="dialog" aria-labelledby="addRoomTypeModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" action="{{ isset($pendingApplications[0]) ? route('warden.hostels.room-types.store', $pendingApplications[0]->hostel_id) : '#' }}">
        @csrf
        @if(isset($pendingApplications[0]))
          <input type="hidden" name="hostel_id" value="{{ $pendingApplications[0]->hostel_id }}">
        @endif
        <div class="modal-header">
          <h5 class="modal-title" id="addRoomTypeModalLabel">Add Room Type</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="roomType">Room Type</label>
            <input type="text" class="form-control" id="roomType" name="type" placeholder="e.g. Single, Double, Triple, Quad" required>
          </div>
          <div class="form-group">
            <label for="capacity">Capacity</label>
            <input type="number" class="form-control" id="capacity" name="capacity" min="1" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Room Type</button>
        </div>
      </form>
    </div>
  </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<!-- Content Row -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Pending Student Applications</h6>
            </div>
            <form method="POST" action="{{ route('warden.room-allotment.bulk_reject') }}" id="bulkRejectForm">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered w-100 mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th><input type="checkbox" id="selectAllReject"></th>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Hostel</th>
                                <th>Room Type</th>
                                <th>Applied Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingApplications as $application)
                                <tr>
                                    <td><input type="checkbox" name="application_ids[]" value="{{ $application->id }}"></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-user fa-2x text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1 ml-3">
                                                <div class="font-weight-bold">{{ $application->student->name ?? '-' }}</div>
                                                <div class="text-muted small">{{ $application->student->phone ?? 'No phone' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $application->student->email }}</td>
                                    <td>
                                        @if($application->status === 'pending' && isset($application->roomAssignments) && $application->roomAssignments && $application->roomAssignments->count() === 0)
                                            &nbsp;
                                        @else
                                            <span class="badge badge-info">{{ $application->hostel->name ?? '-' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($application->status === 'pending' && isset($application->roomAssignments) && $application->roomAssignments && $application->roomAssignments->count() === 0)
                                            &nbsp;
                                        @else
                                            <span class="badge badge-secondary">
                                                {{ $application->roomType->type ?? '-' }} ({{ $application->roomType->capacity ?? '-' }} beds)
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $application->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('warden.room-allotment.show', $application) }}" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-user-plus fa-sm"></i> Allot Room
                                        </a>
                                        <form action="{{ route('warden.applications.update', $application) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to reject this application?');">
                                                <i class="fas fa-times fa-sm"></i> Reject
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mb-3 d-flex justify-content-end align-items-center">
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to reject the selected applications?')">
                        <i class="fas fa-times"></i> Reject Selected
                    </button>
                </div>
            </form>
            <div class="d-flex justify-content-end">
                {{ $pendingApplications->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<!-- Summary Statistics -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Application Summary</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="h4 text-warning">{{ $pendingApplications->count() }}</div>
                            <div class="text-muted">Pending Applications</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="h4 text-info">{{ $pendingApplications->groupBy('hostel_id')->count() }}</div>
                            <div class="text-muted">Hostels with Pending Apps</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="h4 text-primary">{{ $pendingApplications->groupBy('room_type_id')->count() }}</div>
                            <div class="text-muted">Room Types Requested</div>
                        </div>
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
            "order": [[ 4, "desc" ]],
            "pageLength": 10,
            "language": {
                "search": "Search applications:",
                "lengthMenu": "Show _MENU_ applications per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ applications"
            }
        });
    });

    document.getElementById('selectAllReject').addEventListener('change', function() {
        const checked = this.checked;
        document.querySelectorAll('input[name="application_ids[]"]').forEach(cb => cb.checked = checked);
    });
</script>
@endpush 