@extends('layouts.admin')

@section('title', 'Confirm Rejection')

@section('content')
<div class="container-fluid py-4">
    @include('components.breadcrumb', [
        'pageTitle' => $pageTitle,
        'breadcrumbs' => $breadcrumbs
    ])

    <!-- Confirmation Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle"></i> Confirm Application Rejection
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle"></i> Warning
                        </h6>
                        <p class="mb-0">You are about to reject this application. This action cannot be undone.</p>
                    </div>

                    <!-- Application Details -->
                    <div class="row mb-4">
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
                            </table>
                        </div>
                    </div>

                    <!-- Confirmation Form -->
                    <form action="{{ route('warden.applications.update', $application) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="confirmed" value="true">
                        
                        <div class="form-group">
                            <label for="warden_remarks" class="font-weight-bold">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea name="warden_remarks" class="form-control" rows="4" placeholder="Please provide a reason for rejecting this application..." required>{{ $remarks }}</textarea>
                            <small class="form-text text-muted">This reason will be visible to the student.</small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="confirm_rejection" required>
                                <label class="custom-control-label" for="confirm_rejection">
                                    I understand that this action will permanently reject the application and cannot be undone.
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('warden.applications.show', $application) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-danger" id="confirm-btn" disabled>
                                <i class="fas fa-times"></i> Confirm Rejection
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('components.student-profile-modal')

<script>
document.addEventListener('DOMContentLoaded', function() {
    const confirmCheckbox = document.getElementById('confirm_rejection');
    const confirmBtn = document.getElementById('confirm-btn');
    const remarksTextarea = document.querySelector('textarea[name="warden_remarks"]');

    // Enable/disable confirm button based on checkbox and required field
    function updateConfirmButton() {
        const isChecked = confirmCheckbox.checked;
        const hasRemarks = remarksTextarea.value.trim().length > 0;
        confirmBtn.disabled = !isChecked || !hasRemarks;
    }

    confirmCheckbox.addEventListener('change', updateConfirmButton);
    remarksTextarea.addEventListener('input', updateConfirmButton);

    // Initial check
    updateConfirmButton();
});
</script>
@endsection 