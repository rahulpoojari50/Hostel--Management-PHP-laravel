@extends('layouts.admin')

@section('title', 'Application Details')

@section('content')
<div class="container-fluid py-4">
@include('components.breadcrumb-nav', ['breadcrumbs' => $breadcrumbs])

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h5 class="mb-0 text-gray-800">{{ $pageTitle }}</h5>
</div>

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
                                        @php
                                            $displayStatus = $application->getDisplayStatus();
                                            $badgeClass = $displayStatus === 'pending' ? 'warning' : 
                                                         ($displayStatus === 'approved' ? 'success' : 
                                                         ($displayStatus === 'reapproved' ? 'success' : 'danger'));
                                        @endphp
                                        <span class="badge badge-{{ $badgeClass }}">
                                            @if($displayStatus === 'reapproved')
                                                <i class="fas fa-check-double mr-1"></i>Reapproved
                                            @else
                                                {{ ucfirst($displayStatus) }}
                                            @endif
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
                                <form action="{{ route('warden.applications.update', $application) }}" method="POST" id="reject-form">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="action" value="reject">
                                    <div class="form-group">
                                        <label for="warden_remarks_reject">Warden Remarks (optional)</label>
                                        <textarea name="warden_remarks" class="form-control" rows="3" placeholder="Reason for rejection..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-danger" id="reject-btn">
                                        <i class="fas fa-times"></i> Reject Application
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif($application->status == 'rejected')
        <!-- Reapprove Card -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4 border-warning reapprove-section">
                    <div class="card-header py-3 bg-warning text-dark">
                        <h6 class="m-0 font-weight-bold">Reapprove Application</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle"></i> Application Status
                            </h6>
                            <p class="mb-0">This application was previously rejected. You can reapprove it and assign a room to the student.</p>
                        </div>
                        
                        <form action="{{ route('warden.applications.update', $application) }}" method="POST" id="reapprove-form">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="approve">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="room_id">Select Room Number <span class="text-danger">*</span></label>
                                        <select name="room_id" class="form-control" required id="room_id_select">
                                            <option value="">-- Select Room --</option>
                                            @foreach($availableRooms as $room)
                                                <option value="{{ $room->id }}">Room {{ $room->room_number }} (Floor {{ $room->floor }}, {{ $room->current_occupants }}/{{ $room->max_occupants }})</option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Please select an available room for the student.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="warden_remarks_reapprove">Warden Remarks (optional)</label>
                                        <textarea name="warden_remarks" class="form-control" rows="3" placeholder="Reason for reapproval..." id="warden_remarks_reapprove"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-warning btn-lg" id="reapprove-btn">
                                    <i class="fas fa-check-double"></i> Reapprove & Allot Room
                                </button>
                            </div>
                        </form>
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
                    @if($application->status == 'pending' || $application->status == 'rejected')
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

<style>
.reapprove-section {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border: 2px solid #ffc107;
}

.reapprove-section .card-header {
    background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
    color: #000;
    font-weight: bold;
}

.btn-warning:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.form-control.is-valid {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.form-control.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    border: 1px solid #bee5eb;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rejectForm = document.getElementById('reject-form');
    const rejectBtn = document.getElementById('reject-btn');
    const reapproveForm = document.getElementById('reapprove-form');
    const reapproveBtn = document.getElementById('reapprove-btn');
    const roomSelect = document.getElementById('room_id_select');
    
    // Handle reject form
    if (rejectForm && rejectBtn) {
        rejectForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show confirmation dialog
            if (confirm('Are you sure you want to reject this application? This action will require additional confirmation.')) {
                // Submit the form
                rejectForm.submit();
            }
        });
    }
    
    // Handle reapprove form
    if (reapproveForm && reapproveBtn) {
        reapproveForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate room selection
            if (!roomSelect.value) {
                alert('Please select a room before reapproving the application.');
                roomSelect.focus();
                return;
            }
            
            // Show confirmation dialog
            if (confirm('Are you sure you want to reapprove this application and allot a room? This action cannot be undone.')) {
                // Show loading state
                reapproveBtn.disabled = true;
                reapproveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                
                // Submit the form
                reapproveForm.submit();
            }
        });
    }
    
    // Add visual feedback for room selection
    if (roomSelect) {
        roomSelect.addEventListener('change', function() {
            if (this.value) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        });
    }
});
</script>
@endsection 