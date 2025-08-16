@extends('layouts.admin')

@section('title', 'Student Dashboard')

@section('content')
<!-- Page Heading -->

<!-- Page Title -->
<div class="mb-4">
    <h5 class="mb-0 text-gray-800">Student Dashboard</h5>
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
                            <span class="badge badge-secondary text-capitalize">Allocated</span>
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
    <!-- Meal Menu Details Card -->
    <div class="col-lg-12 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Meal Menu Details</h6>
            </div>
            <div class="card-body">
                @if(isset($mealMenu) && count($mealMenu))
                    @php
                        $hasMealMenuData = false;
                        $daysOfWeek = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                        $mealTypes = ['breakfast', 'lunch', 'snacks', 'dinner'];
                        
                        foreach ($daysOfWeek as $day) {
                            foreach ($mealTypes as $type) {
                                if (isset($mealMenu[$day][$type]) && !empty(trim($mealMenu[$day][$type]))) {
                                    $hasMealMenuData = true;
                                    break 2;
                                }
                            }
                        }
                    @endphp
                    
                    @if($hasMealMenuData)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Day</th>
                                        <th class="text-capitalize">Breakfast</th>
                                        <th class="text-capitalize">Lunch</th>
                                        <th class="text-capitalize">Snacks</th>
                                        <th class="text-capitalize">Dinner</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                        <tr>
                                            <td class="font-weight-bold">{{ $day }}</td>
                                            <td>{{ $mealMenu[strtolower($day)]['breakfast'] ?? '-' }}</td>
                                            <td>{{ $mealMenu[strtolower($day)]['lunch'] ?? '-' }}</td>
                                            <td>{{ $mealMenu[strtolower($day)]['snacks'] ?? '-' }}</td>
                                            <td>{{ $mealMenu[strtolower($day)]['dinner'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No meal menu has been set by the warden for your hostel yet.
                        </div>
                    @endif
                @elseif($assignment && $assignment->room && $assignment->room->hostel)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No meal menu has been set by the warden for your hostel yet.
                    </div>
                @elseif($application && $application->isApproved() && $application->hostel)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No meal menu has been set by the warden for your approved hostel yet.
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> You need to be assigned to a hostel to view the meal menu details.
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
