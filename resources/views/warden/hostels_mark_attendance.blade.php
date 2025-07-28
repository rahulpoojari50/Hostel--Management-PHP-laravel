@extends('layouts.admin')

@section('title', 'Mark Hostel Attendance')

@section('content')
@php
    $attendanceExists = isset($records) && $records->count() > 0;
@endphp
<div class="container-fluid py-4">
    @if($attendanceExists && empty($editMode))
        <div class="alert alert-warning mb-3">Attendance already taken for this date. You cannot modify the records.</div>
    @endif
    @if(session('error'))
        <div class="alert alert-warning" id="attendance-error-alert">{{ session('error') }}</div>
        <script>
            setTimeout(function() {
                var alert = document.getElementById('attendance-error-alert');
                if (alert) alert.style.display = 'none';
            }, 3000);
        </script>
    @endif
    @if(session('success'))
        <div class="alert alert-success" id="attendance-success-alert">{{ session('success') }}</div>
        <script>
            setTimeout(function() {
                var alert = document.getElementById('attendance-success-alert');
                if (alert) alert.style.display = 'none';
            }, 3000);
        </script>
    @endif
    <a href="{{ route('warden.hostels.attendance', $hostel->id) }}" class="btn btn-secondary mb-3">&larr; Back</a>
    <h1 class="h3 mb-4 text-gray-800">Mark Attendance for {{ $hostel->name }}</h1>
    <div class="mb-2">
        <strong>Legend:</strong>
        <span class="badge badge-success">P</span> = Present,
        <span class="badge badge-danger">A</span> = Absent,
        <span class="badge badge-warning">L</span> = On Leave,
        <span class="badge badge-info">H</span> = Holiday
    </div>
    <form method="POST" action="{{ route('warden.warden.hostels.attendance.store', $hostel->id) }}">
        @csrf
        <input type="hidden" name="date" value="{{ $date }}">
        @if(!empty($editMode))
            <input type="hidden" name="edit" value="1">
        @endif
        <div class="mb-3">
            <strong>Date:</strong> {{ $date }}
        </div>
        <div class="mb-3">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="mark_all" id="mark_all_present" value="Taken" onclick="markAllHostel('Taken'); this.blur();">
                <label class="form-check-label" for="mark_all_present">All Present</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="mark_all" id="mark_all_absent" value="Skipped" onclick="markAllHostel('Skipped'); this.blur();">
                <label class="form-check-label" for="mark_all_absent">All Absent</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="mark_all" id="mark_all_leave" value="On Leave" onclick="markAllHostel('On Leave'); this.blur();">
                <label class="form-check-label" for="mark_all_leave">All On Leave</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="mark_all" id="mark_all_holiday" value="Holiday" onclick="markAllHostel('Holiday'); this.blur();">
                <label class="form-check-label" for="mark_all_holiday">All Holiday</label>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Student Name</th>
                                <th>USN</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                @php $record = isset($records) ? $records->where('student_id', $student->id)->first() : null; @endphp
                                <tr>
                                    <td>
                                        <a href="#" class="student-name-clickable text-primary" data-student-id="{{ $student->id }}" style="text-decoration: none; cursor: pointer;">
                                            <i class="fas fa-user mr-1"></i>{{ $student->name }}
                                        </a>
                                    </td>
                                    <td>{{ $student->usn ?? '-' }}</td>
                                    <td class="text-center">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input status-radio" type="radio" name="status[{{ $student->id }}]" value="Taken" @if(optional($record)->status === 'Taken' || !$record) checked @endif @if($attendanceExists && empty($editMode)) disabled @endif>
                                            <label class="form-check-label">P</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input status-radio" type="radio" name="status[{{ $student->id }}]" value="Skipped" @if(optional($record)->status === 'Skipped') checked @endif @if($attendanceExists && empty($editMode)) disabled @endif>
                                            <label class="form-check-label">A</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input status-radio" type="radio" name="status[{{ $student->id }}]" value="On Leave" @if(optional($record)->status === 'On Leave') checked @endif @if($attendanceExists && empty($editMode)) disabled @endif>
                                            <label class="form-check-label">L</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input status-radio" type="radio" name="status[{{ $student->id }}]" value="Holiday" @if(optional($record)->status === 'Holiday') checked @endif @if($attendanceExists && empty($editMode)) disabled @endif>
                                            <label class="form-check-label">H</label>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="remarks[{{ $student->id }}]" class="form-control" placeholder="Optional remarks" value="{{ $record->remarks ?? '' }}" @if($attendanceExists && empty($editMode)) readonly @endif>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button class="btn btn-success mt-3" @if($attendanceExists && empty($editMode)) disabled @endif>Submit Attendance</button>
            </div>
        </div>
    </form>
</div>
<script>
function markAllHostel(status) {
    document.querySelectorAll('.status-radio').forEach(function(radio) {
        if (radio.value === status) radio.checked = true;
    });
}

// --- Instant attendance-exists check ---
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.querySelector('input[name="date"]');
    if (!dateInput) return;
    dateInput.addEventListener('change', function() {
        const date = this.value;
        const hostelId = {{ $hostel->id }};
        fetch(`/api/hostels/${hostelId}/attendance-exists?date=${date}`)
            .then(res => res.json())
            .then(data => {
                const warning = document.getElementById('attendance-exists-warning');
                if (data.exists) {
                    warning.textContent = 'Attendance already taken for this date.';
                    warning.style.display = '';
                } else {
                    warning.textContent = '';
                    warning.style.display = 'none';
                }
            });
    });
});
</script>

@include('components.student-profile-modal')
@endsection 