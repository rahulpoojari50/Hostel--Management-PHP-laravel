@extends('layouts.admin')

@section('title', 'Student Dashboard')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Student Dashboard</h1>
</div>

<div class="row">
    <!-- Application Status Card -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Current Application Status</h6>
            </div>
            <div class="card-body">
                @if($application)
                    <div class="row g-3 mb-2">
                        <div class="col-6 col-md-4"><strong>Hostel:</strong> {{ $application->hostel->name ?? '-' }}</div>
                        <div class="col-6 col-md-4"><strong>Room Type:</strong> {{ $application->roomType->type ?? '-' }}</div>
                        <div class="col-6 col-md-4"><strong>Date:</strong> {{ $application->application_date }}</div>
                        <div class="col-6 col-md-4">
                            <strong>Status:</strong>
                            <span class="badge badge-{{ $application->status === 'pending' ? 'warning' : ($application->status === 'approved' ? 'success' : 'danger') }} text-capitalize">
                                {{ $application->status }}
                            </span>
                        </div>
                        <div class="col-12"><strong>Warden Remarks:</strong> {{ $application->warden_remarks ?? '-' }}</div>
                    </div>
                @else
                    <p class="mb-0 text-muted">No application submitted yet.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Room Details Card -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Room Details</h6>
            </div>
            <div class="card-body">
                @if($assignment && $assignment->room)
                    <div class="row g-3 mb-2">
                        <div class="col-6 col-md-4"><strong>Hostel:</strong> {{ $assignment->room->hostel->name ?? '-' }}</div>
                        <div class="col-6 col-md-4"><strong>Room #:</strong> {{ $assignment->room->room_number }}</div>
                        <div class="col-6 col-md-4"><strong>Type:</strong> {{ $assignment->room->roomType->type ?? '-' }}</div>
                        <div class="col-6 col-md-4"><strong>Floor:</strong> {{ $assignment->room->floor }}</div>
                        <div class="col-6 col-md-4">
                            <strong>Status:</strong>
                            <span class="badge badge-secondary text-capitalize">{{ $assignment->room->status }}</span>
                        </div>
                        <div class="col-6 col-md-4"><strong>Assigned:</strong> {{ $assignment->assigned_date }}</div>
                    </div>
                @else
                    <p class="mb-0 text-muted">No room assigned yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Weekly Meals Menu Card -->
    <div class="col-lg-12 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Weekly Meals Menu (Set by Warden)</h6>
            </div>
            <div class="card-body">
                @if(isset($weeklyMenu) && count($weeklyMenu))
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    @foreach($menuMealTypes as $type)
                                        <th class="text-capitalize">{{ $type }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($daysOfWeek as $day)
                                    <tr>
                                        <td class="font-weight-bold">{{ $day }}</td>
                                        @foreach($menuMealTypes as $type)
                                            <td>{{ $weeklyMenu[$day][$type] ?? '-' }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif($assignment && $assignment->room && $assignment->room->hostel)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No weekly menu has been set by the warden for your hostel yet.
                    </div>
                @elseif($application && $application->isApproved() && $application->hostel)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No weekly menu has been set by the warden for your approved hostel yet.
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> You need to be assigned to a hostel to view the weekly meals menu.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "order": [[ 1, "asc" ]],
            "pageLength": 5,
            "language": {
                "search": "Search meals:",
                "lengthMenu": "Show _MENU_ meals per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ meals"
            }
        });
    });
</script>
@endpush
