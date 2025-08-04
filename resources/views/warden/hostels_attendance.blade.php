@extends('layouts.admin')

@section('title', 'Hostel Attendance')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <!-- Breadcrumb Navigation -->
        @include('components.breadcrumb-nav', ['breadcrumbs' => $breadcrumbs])
    </div>
    <div>
        {{-- Action buttons can go here --}}
    </div>
</div>

<!-- Page Title -->
<div class="mb-4">
    <h5 class="mb-0 text-gray-800">Hostel Attendance</h5>
</div>

<div class="container-fluid py-4">
    <div class="mb-2">
        <strong>Legend:</strong>
        <span class="badge badge-success">P</span> = Present,
        <span class="badge badge-danger">A</span> = Absent,
        <span class="badge badge-warning">L</span> = On Leave,
        <span class="badge badge-info">H</span> = Holiday
    </div>
    <form method="GET" class="mb-4 d-flex align-items-end" id="attendanceForm">
        <button type="button" class="btn btn-info ml-2" id="viewAttendanceBtn">View Attendance</button>
        <button type="button" class="btn btn-primary ml-2" id="takeAttendanceBtn">Take Attendance</button>
        <button type="button" class="btn btn-warning ml-2" id="editAttendanceBtn">Edit Attendance</button>
        <input type="hidden" name="date" id="hiddenAttendanceDate" value="{{ $date }}">
    </form>

    <!-- Modal for date selection -->
    <div class="modal fade" id="dateModal" tabindex="-1" role="dialog" aria-labelledby="dateModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="dateModalLabel">Select Date</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input type="date" class="form-control" id="modalAttendanceDate" required>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="submitDateBtn">Submit</button>
          </div>
        </div>
      </div>
    </div>

    @if(request('date'))
        @if((request('edit') || request('action') == 'take') && isset($attendanceExists) && $attendanceExists)
            <div aria-live="polite" aria-atomic="true" style="position: fixed; top: 1rem; right: 1rem; min-width: 300px; z-index: 9999;">
                <div class="toast show" id="attendance-exists-toast" data-delay="3000" style="background: #fff3cd; border-color: #ffeeba;">
                    <div class="toast-header" style="background: #fff3cd; border-bottom: 1px solid #ffeeba;">
                        <strong class="mr-auto text-warning">Notice</strong>
                        <small>Now</small>
                        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close" onclick="document.getElementById('attendance-exists-toast').style.display='none';">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="toast-body text-dark">
                        Attendance already taken for this date.
                    </div>
                </div>
            </div>
            <script>
                setTimeout(function() {
                    var toast = document.getElementById('attendance-exists-toast');
                    if (toast) toast.style.display = 'none';
                }, 3000);
            </script>
        @endif
        @if(isset($students) && count($students))
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
                            @php
                                $record = $records[$student->id][$date][0] ?? null;
                                $status = $record->status ?? null;
                                $remarks = $record->remarks ?? '';
                            @endphp
                                                            <tr>
                                    <td>
                                        <a href="#" class="student-name-clickable text-primary" data-student-id="{{ $student->id }}" style="text-decoration: none; cursor: pointer;">
                                            <i class="fas fa-user mr-1"></i>{{ $student->name }}
                                        </a>
                                    </td>
                                <td>{{ $student->usn ?? '-' }}</td>
                                <td>
                                    @if($status === 'Taken')
                                        <span class="badge badge-success">P</span>
                                    @elseif($status === 'Skipped')
                                        <span class="badge badge-danger">A</span>
                                    @elseif($status === 'On Leave')
                                        <span class="badge badge-warning">L</span>
                                    @elseif($status === 'Holiday')
                                        <span class="badge badge-info">H</span>
                                    @else
                                        <span class="badge badge-secondary">-</span>
                                    @endif
                                </td>
                                <td>{{ $remarks }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
            <div class="alert alert-info mt-4">No students found for this hostel.</div>
        @endif
    @endif
</div>
<script>
// Helper to format date as yyyy-mm-dd
function formatDate(date) {
    var d = new Date(date), month = '' + (d.getMonth() + 1), day = '' + d.getDate(), year = d.getFullYear();
    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;
    return [year, month, day].join('-');
}

function restrictDatePicker() {
    var dateInput = document.getElementById('attendanceDate');
    if (!dateInput) return;
    var today = new Date();
    var minDate = new Date();
    minDate.setDate(today.getDate() - 3);
    dateInput.setAttribute('max', formatDate(today));
    dateInput.setAttribute('min', formatDate(minDate));
}

function unrestrictDatePicker() {
    var dateInput = document.getElementById('attendanceDate');
    if (!dateInput) return;
    dateInput.removeAttribute('min');
    dateInput.removeAttribute('max');
}

let modalAction = null; // 'view', 'take', or 'edit'
document.addEventListener('DOMContentLoaded', function() {
    // Show modal on View Attendance click (all dates enabled)
    document.getElementById('viewAttendanceBtn')?.addEventListener('click', function(e) {
        e.preventDefault();
        modalAction = 'view';
        document.getElementById('modalAttendanceDate').removeAttribute('min');
        document.getElementById('modalAttendanceDate').removeAttribute('max');
        $('#dateModal').modal('show');
    });
    // Show modal on Take Attendance click (restrict dates)
    document.getElementById('takeAttendanceBtn')?.addEventListener('click', function(e) {
        e.preventDefault();
        modalAction = 'take';
        var dateInput = document.getElementById('modalAttendanceDate');
        var today = new Date();
        var minDate = new Date();
        minDate.setDate(today.getDate() - 3);
        dateInput.setAttribute('max', formatDate(today));
        dateInput.setAttribute('min', formatDate(minDate));
        $('#dateModal').modal('show');
    });
    // Show modal on Edit Attendance click (restrict dates)
    document.getElementById('editAttendanceBtn')?.addEventListener('click', function(e) {
        e.preventDefault();
        modalAction = 'edit';
        var dateInput = document.getElementById('modalAttendanceDate');
        var today = new Date();
        var minDate = new Date();
        minDate.setDate(today.getDate() - 3);
        dateInput.setAttribute('max', formatDate(today));
        dateInput.setAttribute('min', formatDate(minDate));
        $('#dateModal').modal('show');
    });
    // On modal submit, handle according to action
    document.getElementById('submitDateBtn')?.addEventListener('click', function() {
        var modalDate = document.getElementById('modalAttendanceDate').value;
        if (!modalDate) {
            document.getElementById('modalAttendanceDate').focus();
            return;
        }
        if (modalAction === 'view') {
            document.getElementById('hiddenAttendanceDate').value = modalDate;
            document.getElementById('attendanceForm').submit();
        } else if (modalAction === 'take') {
            // Redirect to take attendance page for selected date
            var takeUrl = "{{ route('warden.hostels.attendance.mark', [$hostel->id]) }}";
            takeUrl += '?date=' + encodeURIComponent(modalDate);
            window.location.href = takeUrl;
        } else if (modalAction === 'edit') {
            // Redirect to edit attendance page for selected date
            var editUrl = "{{ route('warden.hostels.attendance.mark', [$hostel->id]) }}";
            editUrl += '?date=' + encodeURIComponent(modalDate) + '&edit=1';
            window.location.href = editUrl;
        }
        $('#dateModal').modal('hide');
    });
});
function updateEditAttendanceBtn() {
    var editBtn = document.getElementById('editAttendanceBtn');
    if (!editBtn) return;
    var dateStr = document.getElementById('attendanceDate').value;
    if (!dateStr) { editBtn.style.display = 'none'; return; }
    var selected = new Date(dateStr);
    selected.setHours(0,0,0,0);
    var today = new Date();
    today.setHours(0,0,0,0);
    var minDate = new Date(today);
    minDate.setDate(today.getDate() - 3);
    if (selected >= minDate && selected <= today) {
        editBtn.disabled = false;
        editBtn.style.display = '';
    } else {
        editBtn.disabled = true;
        editBtn.style.display = '';
    }
}
document.getElementById('attendanceDate')?.addEventListener('change', updateEditAttendanceBtn);
document.addEventListener('DOMContentLoaded', updateEditAttendanceBtn);
</script>
@endsection 