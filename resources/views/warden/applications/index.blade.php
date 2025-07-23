@extends('layouts.admin')

@section('title', 'Room Applications')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Room Applications</h1>
</div>

<!-- Content Row -->
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">All Applications</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Hostel</th>
                                <th>Room Type</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applications as $app)
                                <tr>
                                    <td>{{ $app->student->name ?? '-' }}</td>
                                    <td>{{ $app->hostel->name ?? '-' }}</td>
                                    <td>{{ $app->roomType->type ?? '-' }}</td>
                                    <td>{{ $app->application_date }}</td>
                                    <td>
                                        <span class="badge badge-{{ $app->status === 'pending' ? 'warning' : ($app->status === 'approved' ? 'success' : 'danger') }}">
                                            {{ ucfirst($app->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @if($app->status == 'pending')
                                                <form action="{{ route('warden.applications.update', $app) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('warden.applications.update', $app) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="action" value="reject">
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Reject">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('warden.applications.show', $app) }}" 
                                               class="btn btn-info btn-sm" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                        No applications found.
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
            "order": [[ 3, "desc" ]],
            "pageLength": 10,
            "language": {
                "search": "Search applications:",
                "lengthMenu": "Show _MENU_ applications per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ applications"
            }
        });
    });
</script>
@endpush 