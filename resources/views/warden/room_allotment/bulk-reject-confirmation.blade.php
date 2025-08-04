@extends('layouts.admin')

@section('title', 'Confirm Bulk Rejection')

@section('content')
<div class="container-fluid py-4">
    @include('components.breadcrumb', [
        'pageTitle' => 'Confirm Bulk Rejection',
        'breadcrumbs' => [
            ['name' => 'Home', 'url' => url('/warden/dashboard')],
            ['name' => 'Room Allotment', 'url' => route('warden.room-allotment.index')],
            ['name' => 'Confirm Bulk Rejection', 'url' => '']
        ]
    ])

    <!-- Confirmation Card -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle"></i> Confirm Bulk Application Rejection
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle"></i> Warning
                        </h6>
                        <p class="mb-0">You are about to reject <strong>{{ count($applications) }} application(s)</strong>. This action cannot be undone.</p>
                    </div>

                    <!-- Applications to be rejected -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold text-primary">Applications to be rejected:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Email</th>
                                        <th>Hostel</th>
                                        <th>Room Type</th>
                                        <th>Application Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($applications as $application)
                                    <tr>
                                        <td>
                                            @if($application->student)
                                                <a href="#" class="student-name-clickable text-primary" data-student-id="{{ $application->student->id }}" style="text-decoration: none; cursor: pointer;">
                                                    <i class="fas fa-user mr-1"></i>{{ $application->student->name }}
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $application->student->email ?? '-' }}</td>
                                        <td>{{ $application->hostel->name ?? '-' }}</td>
                                        <td>{{ $application->roomType->type ?? '-' }}</td>
                                        <td>{{ $application->application_date }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Confirmation Form -->
                    <form action="{{ route('warden.room-allotment.bulk_reject') }}" method="POST">
                        @csrf
                        @foreach($applicationIds as $id)
                            <input type="hidden" name="application_ids[]" value="{{ $id }}">
                        @endforeach
                        <input type="hidden" name="confirmed" value="true">
                        
                        <div class="form-group">
                            <label for="warden_remarks" class="font-weight-bold">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea name="warden_remarks" class="form-control" rows="4" placeholder="Please provide a reason for rejecting these applications..." required>Bulk rejected</textarea>
                            <small class="form-text text-muted">This reason will be visible to all affected students.</small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="confirm_bulk_rejection" required>
                                <label class="custom-control-label" for="confirm_bulk_rejection">
                                    I understand that this action will permanently reject {{ count($applications) }} application(s) and cannot be undone.
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('warden.room-allotment.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-danger" id="confirm-btn" disabled>
                                <i class="fas fa-times"></i> Confirm Bulk Rejection ({{ count($applications) }} applications)
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
    const confirmCheckbox = document.getElementById('confirm_bulk_rejection');
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