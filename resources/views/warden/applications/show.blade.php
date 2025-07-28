@extends('layouts.admin')

@section('title', 'Application Details')

@section('content')
<div class="container-fluid py-4">
    @include('components.breadcrumb', [
        'pageTitle' => 'Application Details',
        'breadcrumbs' => [
            ['name' => 'Home', 'url' => url('/')],
            ['name' => 'Applications', 'url' => route('warden.applications.index')],
            ['name' => 'Application Details', 'url' => '']
        ]
    ])

    <!-- Application Details Card -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Application Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-primary">Student Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>
                                        @if($application->student)
                                            <a href="#" class="student-name-clickable text-primary" data-student-id="{{ $application->student->id }}" style="text-decoration: none; cursor: pointer;">
                                                <i class="fas fa-user mr-1"></i>{{ $application->student->name }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $application->student->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $application->student->phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td>{{ $application->student->address ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-primary">Application Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Hostel:</strong></td>
                                    <td>{{ $application->hostel->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Room Type:</strong></td>
                                    <td>{{ $application->roomType->type ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Application Date:</strong></td>
                                    <td>{{ $application->application_date }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $application->status === 'pending' ? 'warning' : ($application->status === 'approved' ? 'success' : 'danger') }}">
                                            {{ ucfirst($application->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @if($application->warden_remarks)
                                <tr>
                                    <td><strong>Warden Remarks:</strong></td>
                                    <td>{{ $application->warden_remarks }}</td>
                                </tr>
                                @endif
                                @if($application->processedBy)
                                <tr>
                                    <td><strong>Processed By:</strong></td>
                                    <td>{{ $application->processedBy->name }}</td>
                                </tr>
                                @endif
                                @if($application->processed_at)
                                <tr>
                                    <td><strong>Processed At:</strong></td>
                                    <td>{{ $application->processed_at }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($application->status == 'pending')
        <!-- Room Selection Card -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Process Application</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-success">Approve & Allot Room</h6>
                                <form action="{{ route('warden.applications.update', $application) }}" method="POST" class="mb-4">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="action" value="approve">
                                    <div class="form-group">
                                        <label for="room_id">Select Room Number <span class="text-danger">*</span></label>
                                        <select name="room_id" class="form-control" required>
                                            <option value="">-- Select Room --</option>
                                            @foreach($availableRooms as $room)
                                                <option value="{{ $room->id }}">Room {{ $room->room_number }} (Floor {{ $room->floor }}, {{ $room->current_occupants }}/{{ $room->max_occupants }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="warden_remarks_approve">Warden Remarks (optional)</label>
                                        <textarea name="warden_remarks" class="form-control" rows="3" placeholder="Any additional notes..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check"></i> Approve & Allot Room
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-danger">Reject Application</h6>
                                <form action="{{ route('warden.applications.update', $application) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="action" value="reject">
                                    <div class="form-group">
                                        <label for="warden_remarks_reject">Warden Remarks (optional)</label>
                                        <textarea name="warden_remarks" class="form-control" rows="3" placeholder="Reason for rejection..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-times"></i> Reject Application
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-body">
                    <a href="{{ route('warden.applications.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Applications
                    </a>
                    @if($application->status == 'pending')
                        <a href="{{ route('warden.room-allotment.show', $application) }}" class="btn btn-primary">
                            <i class="fas fa-bed"></i> Go to Room Allotment
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('components.student-profile-modal')
@endsection 