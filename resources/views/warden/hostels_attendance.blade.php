@extends('layouts.admin')

@section('title', 'Hostel Attendance')

@section('content')
<div id="main-attendance-content" class="container-fluid py-4">
    {{-- <h1 class="h3 mb-4 text-gray-800">Attendance for {{ $hostel->name }}</h1> --}}

    @include('components.breadcrumb', [
        'pageTitle' => 'Hostel Attendance',
        'breadcrumbs' => [
            ['name' => 'Home', 'url' => url('/')],
            ['name' => 'Hostels Management', 'url' => route('warden.hostels.index')],
            ['name' => 'Hostel Attendance', 'url' => '']
        ]
    ])
    @php
        $dateSelected = !empty(request('date'));
        $viewOnly = request('view');
    @endphp
    <form method="GET" class="mb-4 d-flex align-items-end" id="attendanceForm">
        <div class="form-row align-items-end">
            <div class="col-auto input-group">
                <label class="mr-2">Date</label>
                @php
                    // Remove minDate, maxDate, isEditable logic
                @endphp
                <input type="date" name="date" id="attendanceDate" class="form-control" value="{{ $date }}">
                <div class="input-group-append">
                    <button type="button" class="btn btn-sm btn-primary" id="applyDateBtn">Apply</button>
                </div>
            </div>
        </div>
        @if($dateSelected)
            <button type="button" class="btn btn-primary ml-2" id="takeAttendanceBtn" @if($attendanceExists) disabled @endif>Take Attendance</button>
            <button type="button" class="btn btn-info ml-2" id="viewAttendanceBtn">View Attendance</button>
            <a href="{{ route('warden.hostels.attendance.download-csv', [$hostel->id, 'date' => $date]) }}" class="btn btn-success ml-2">Download CSV</a>
        @endif
    </form>
    {{-- END FORM BLOCK --}}
    @if($dateSelected)
        @if($attendanceExists)
            <div class="mb-2">
                <span class="text-danger font-weight-bold">Attendance already taken for this date.</span>
            </div>
        @endif
        {{-- Remove the legend --}}
        {{-- <div class="mb-2">
            <strong>Legend:</strong>
            <span class="badge badge-success">P</span> = Present,
            <span class="badge badge-danger">A</span> = Absent
        </div> --}}
        @if($students->count())
            @if($viewOnly)
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Room No</th>
                                        <th>Status</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $student)
                                        @php $record = $records->where('student_id', $student->id)->first(); @endphp
                                        <tr>
                                            <td>{{ $student->name }}</td>
                                            <td>{{ $student->roomAssignments->first()->room->room_number ?? '-' }}</td>
                                            <td>
                                                @if($record)
                                                    @if($record->status === 'Taken')
                                                        <span class="badge badge-success">P</span>
                                                    @elseif($record->status === 'Skipped')
                                                        <span class="badge badge-danger">A</span>
                                                    @else
                                                        <span class="badge badge-secondary">-</span>
                                                    @endif
                                                @else
                                                    <span class="badge badge-secondary">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $record->remarks ?? '' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                @if(request('take'))
                <form method="POST" action="{{ route('warden.warden.hostels.attendance.store', $hostel->id) }}" id="markAttendanceForm">
                    @csrf
                    <input type="hidden" name="date" id="markAttendanceDate" value="{{ $date }}">
                    @if($records->count())
                    <div class="alert alert-warning">You have already taken attendance for this date.</div>
                    @endif
                    <div class="mb-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="mark_all" id="mark_all_present" value="Taken" onclick="markAllHostel('Taken'); this.blur();">
                            <label class="form-check-label" for="mark_all_present">All Present</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="mark_all" id="mark_all_absent" value="Skipped" onclick="markAllHostel('Skipped'); this.blur();">
                            <label class="form-check-label" for="mark_all_absent">All Absent</label>
                        </div>
                    </div>
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Room No</th>
                                            <th>Status</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($students as $student)
                                        <tr>
                                            <td>{{ $student->name }}</td>
                                            <td>{{ $student->roomAssignments->first()->room->room_number ?? '-' }}</td>
                                            <td class="text-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input hostel-radio" type="radio" name="status[{{ $student->id }}]" value="Taken" checked @if($records->count()) disabled @endif>
                                                    <label class="form-check-label">P</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input hostel-radio" type="radio" name="status[{{ $student->id }}]" value="Skipped" @if($records->count()) disabled @endif>
                                                    <label class="form-check-label">A</label>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" name="remarks[{{ $student->id }}]" class="form-control form-control-sm" value="{{ $records->where('student_id', $student->id)->first()->remarks ?? '' }}" @if($records->count()) disabled @endif>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <button class="btn btn-success mt-3" @if($records->count()) disabled @endif>Submit</button>
                        </div>
                    </div>
                </form>
                <script>
                function markAllHostel(status) {
                    document.querySelectorAll('.hostel-radio').forEach(function(radio) {
                        if (radio.value === status) radio.checked = true;
                    });
                }
                </script>
                @endif
            @endif
        @else
            <div class="alert alert-info mt-4">No students found for this hostel.</div>
        @endif
    @endif
<a href="#" class="btn btn-success ml-2" data-toggle="modal" data-target="#exportSummaryModal">Export Report</a>
</div>
<!-- Export Summary Modal -->
<div class="modal fade" id="exportSummaryModal" tabindex="-1" role="dialog" aria-labelledby="exportSummaryModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exportSummaryModalLabel">Export Attendance Summary</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="GET" action="{{ route('warden.hostels.attendance.export-summary', $hostel->id) }}" target="_blank">
        <div class="modal-body">
          <div class="form-group">
            <label for="start_date">Start Date</label>
            <input type="date" class="form-control" name="start_date" id="start_date" required>
          </div>
          <div class="form-group">
            <label for="end_date">End Date</label>
            <input type="date" class="form-control" name="end_date" id="end_date" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Export</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div id="edit-attendance-form"></div>
<div id="edit-attendance-error"></div>
<script>
let hostelId = {{ $hostel->id }};
let attendanceDate = @json($date);
let lastAppliedDate = document.getElementById('attendanceDate').value;
document.getElementById('applyDateBtn')?.addEventListener('click', function() {
    var date = document.getElementById('attendanceDate').value;
    var url = window.location.pathname + '?date=' + encodeURIComponent(date);
    window.location.href = url;
});
document.getElementById('attendanceDate')?.addEventListener('change', function() {
    // Optionally, you could visually enable the Apply button only when the date changes
    // For now, just allow Apply to work as above
});
function markAll(status) {
    document.querySelectorAll('.status-radio').forEach(function(radio) {
        if (radio.value === status) radio.checked = true;
    });
}
document.getElementById('takeAttendanceBtn')?.addEventListener('click', function() {
    var date = document.getElementById('attendanceDate').value;
    var url = "{{ route('warden.hostels.attendance', [$hostel->id]) }}";
    url += '?date=' + encodeURIComponent(date) + '&take=1';
    window.location.href = url;
});
document.getElementById('markAttendanceForm')?.addEventListener('submit', function(e) {
    var date = document.getElementById('attendanceDate').value;
    document.getElementById('markAttendanceDate').value = date;
});
document.getElementById('viewAttendanceBtn')?.addEventListener('click', function() {
    var date = document.getElementById('attendanceDate').value;
    var url = window.location.pathname + '?date=' + encodeURIComponent(date) + '&view=1';
    window.location.href = url;
});
document.getElementById('editAttendanceBtn')?.addEventListener('click', function(e) {
    e.preventDefault();
    // Date range validation: only allow editing for today and previous 4 days (using date strings to avoid timezone issues)
    // (Keep this restriction for editing only)
    const selectedDateStr = attendanceDate;
    const todayObj = new Date();
    todayObj.setHours(0,0,0,0);
    const yyyy = todayObj.getFullYear();
    const mm = String(todayObj.getMonth() + 1).padStart(2, '0');
    const dd = String(todayObj.getDate()).padStart(2, '0');
    const todayStr = `${yyyy}-${mm}-${dd}`;
    // Calculate min date string (previous 4 days)
    const minDateObj = new Date(todayObj);
    minDateObj.setDate(todayObj.getDate() - 4);
    const minY = minDateObj.getFullYear();
    const minM = String(minDateObj.getMonth() + 1).padStart(2, '0');
    const minD = String(minDateObj.getDate()).padStart(2, '0');
    const minDateStr = `${minY}-${minM}-${minD}`;
    if (selectedDateStr < minDateStr || selectedDateStr > todayStr) {
        const errorDiv = document.getElementById('edit-attendance-error');
        errorDiv.innerHTML = '<div class="alert alert-danger mt-3">You cannot edit attendance for this date. Editing is only allowed for today and the previous 4 days.</div>';
        setTimeout(() => { errorDiv.innerHTML = ''; }, 4000);
        return;
    }
    const url = this.getAttribute('data-edit-url');
    const formDiv = document.getElementById('edit-attendance-form');
    const mainContent = document.getElementById('main-attendance-content');
    formDiv.innerHTML = '<div class="text-center my-3">Loading form...</div>';
    mainContent.style.display = 'none';
    fetch(url)
        .then(res => res.text())
        .then(html => {
            formDiv.innerHTML = html;
            const form = formDiv.querySelector('form');
            // Attach Back button event listener
            const backBtn = formDiv.querySelector('#backToAttendanceBtn');
            if (backBtn) {
                backBtn.addEventListener('click', function() {
                    window.location.href = `/warden/hostels/${hostelId}/attendance?date=${attendanceDate}&view=1`;
                });
            }
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(form);
                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': form.querySelector('input[name=_token]').value
                        },
                        body: formData
                    })
                    .then(res => res.ok ? res.text() : Promise.reject(res))
                    .then(() => {
                        formDiv.innerHTML = '<div class="alert alert-success mt-3">Attendance updated!</div>';
                        setTimeout(() => {
                            formDiv.innerHTML = '';
                            mainContent.style.display = '';
                        }, 1500);
                    })
                    .catch(() => {
                        formDiv.innerHTML += '<div class="alert alert-danger mt-3">Failed to update attendance.</div>';
                    });
                });
            }
        });
});
</script>
@if(session('success'))
<script>
    setTimeout(() => {
        toastr.success("{{ session('success') }}");
    }, 500);
</script>
@endif
@endsection 