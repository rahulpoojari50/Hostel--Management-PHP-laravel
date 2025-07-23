@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    {{-- <h1 class="h3 mb-4 text-gray-800">Meals Attendance</h1> --}}
    @if($selectedHostel)
        <div class="mb-3">
            <h4>{{ $selectedHostel->name }}</h4>
        </div>
    @endif
    <div class="mb-2">
        <strong>Legend:</strong>
        <span class="badge badge-success">P</span> = Present,
        <span class="badge badge-danger">A</span> = Absent,
        <span class="badge badge-warning">L</span> = On Leave,
        <span class="badge badge-info">H</span> = Holiday
    </div>
    <form method="GET" class="mb-4 d-flex align-items-end" id="attendanceForm">
        <div class="form-row align-items-end">
            <div class="col-auto input-group">
                <label class="mr-2">Date</label>
                <input type="date" name="date" id="attendanceDate" class="form-control" value="{{ $date }}">
                <div class="input-group-append">
                    <button type="button" class="btn btn-sm btn-primary" id="applyDateBtn">Apply</button>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-info ml-2">View Attendance</button>
        <button type="button" class="btn btn-primary ml-2" id="takeAttendanceBtn">Take Attendance</button>
        @if(!request('take'))
        <a href="{{ route('warden.meals-attendance.download-csv', [$selectedHostel->id, 'date' => $date]) }}" class="btn btn-success ml-2">Download CSV</a>
        @endif
        @if($selectedHostel && $students->count() && !request('edit'))
            <button type="button" class="btn btn-warning ml-2" id="editMealsAttendanceBtn">Edit Attendance</button>
        @endif
    </form>
    <div id="edit-meals-attendance-error"></div>
    @if($selectedHostel && $students->count())
        @if(request('edit'))
            {{-- EDIT TABLE ONLY --}}
            <form method="POST" action="{{ route('warden.meals-attendance.store', $selectedHostel->id) }}">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">
                <div class="mb-3">
                    <strong>Date:</strong> {{ $date }}
                </div>
                <div class="mb-3">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="mark_all" id="mark_all_present" value="Taken" onclick="markAllMeals('Taken'); this.blur();">
                        <label class="form-check-label" for="mark_all_present">All Present</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="mark_all" id="mark_all_absent" value="Skipped" onclick="markAllMeals('Skipped'); this.blur();">
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
                                        <th>Breakfast</th>
                                        <th>Lunch</th>
                                        <th>Snacks</th>
                                        <th>Dinner</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $student)
                                    <tr>
                                        <td>{{ $student->name }}</td>
                                        @foreach(['Breakfast','Lunch','Snacks','Dinner'] as $meal)
                                        <td class="text-center">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input status-radio" type="radio" name="status[{{ $student->id }}][{{ $meal }}]" value="Taken" @if(isset($attendance[$meal][$student->id]) && $attendance[$meal][$student->id] == 'Taken') checked @endif>
                                                <label class="form-check-label">P</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input status-radio" type="radio" name="status[{{ $student->id }}][{{ $meal }}]" value="Skipped" @if(isset($attendance[$meal][$student->id]) && $attendance[$meal][$student->id] == 'Skipped') checked @endif>
                                                <label class="form-check-label">A</label>
                                            </div>
                                        </td>
                                        @endforeach
                                        <td>
                                            <input type="text" name="remarks[{{ $student->id }}]" class="form-control" value="{{ $attendance['remarks'][$student->id] ?? '' }}">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button class="btn btn-success mt-3">Update Attendance</button>
                    </div>
                </div>
            </form>
            <script>
            function markAllMeals(status) {
                document.querySelectorAll('.status-radio').forEach(function(radio) {
                    if (radio.value === status) radio.checked = true;
                });
            }
            </script>
        @elseif(request('take'))
            {{-- TAKE ATTENDANCE TABLE --}}
            @if($attendanceExists)
                <div class="alert alert-warning">You have already taken attendance for this date.</div>
            @endif
            <form method="POST" action="{{ route('warden.meals-attendance.store', $selectedHostel->id) }}" id="markAttendanceForm">
                @csrf
                <input type="hidden" name="date" id="markAttendanceDate" value="{{ $date }}">
                <div class="mb-3">
                    <strong>Date:</strong> {{ $date }}
                </div>
                <div class="mb-3">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="mark_all" id="mark_all_present" value="Taken" onclick="markAllMeals('Taken'); this.blur();">
                        <label class="form-check-label" for="mark_all_present">All Present</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="mark_all" id="mark_all_absent" value="Skipped" onclick="markAllMeals('Skipped'); this.blur();">
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
                                        <th>Breakfast</th>
                                        <th>Lunch</th>
                                        <th>Snacks</th>
                                        <th>Dinner</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $student)
                                    <tr>
                                        <td>{{ $student->name }}</td>
                                        @foreach(['Breakfast','Lunch','Snacks','Dinner'] as $meal)
                                        <td class="text-center">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input status-radio" type="radio" name="status[{{ $student->id }}][{{ $meal }}]" value="Taken" checked @if($attendanceExists) disabled @endif>
                                                <label class="form-check-label">P</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input status-radio" type="radio" name="status[{{ $student->id }}][{{ $meal }}]" value="Skipped" @if($attendanceExists) disabled @endif>
                                                <label class="form-check-label">A</label>
                                            </div>
                                        </td>
                                        @endforeach
                                        <td>
                                            <input type="text" name="remarks[{{ $student->id }}]" class="form-control" @if($attendanceExists) disabled @endif>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button class="btn btn-success mt-3" @if($attendanceExists) disabled @endif>Submit Attendance</button>
                    </div>
                </div>
            </form>
            <script>
            function markAllMeals(status) {
                document.querySelectorAll('.status-radio').forEach(function(radio) {
                    if (radio.value === status) radio.checked = true;
                });
            }
            </script>
        @else
            {{-- VIEW TABLE ONLY --}}
            @php // DEBUG: Show attendance array for troubleshooting
            // Remove this after confirming fix
            // echo '<pre>' . print_r($attendance, true) . '</pre>';
            @endphp
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Student Name</th>
                                    <th>Breakfast</th>
                                    <th>Lunch</th>
                                    <th>Snacks</th>
                                    <th>Dinner</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                <tr>
                                    <td>{{ $student->name }}</td>
                                    @foreach(['Breakfast','Lunch','Snacks','Dinner'] as $meal)
                                    <td>
                                        @php $status = $attendance[$meal][$student->id] ?? null; @endphp
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
                                    @endforeach
                                    <td>{{ $attendance['remarks'][$student->id] ?? '' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @elseif($selectedHostel)
        <div class="alert alert-info mt-4">No students found for this hostel.</div>
    @endif
    @if($selectedHostel && $students->count())
        <div class="mt-3">
            <a href="#" class="btn btn-success" data-toggle="modal" data-target="#exportMealsSummaryModal">Export Report</a>
        </div>
    @endif
</div>
<!-- Export Meals Summary Modal -->
<div class="modal fade" id="exportMealsSummaryModal" tabindex="-1" role="dialog" aria-labelledby="exportMealsSummaryModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exportMealsSummaryModalLabel">Export Meals Attendance Report</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="GET" action="{{ route('warden.meals-attendance.export-summary', $selectedHostel->id) }}" target="_blank">
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
<script>
function markAllMeals(status) {
    document.querySelectorAll('.status-radio').forEach(function(radio) {
        if (radio.value === status) radio.checked = true;
    });
}
document.getElementById('takeAttendanceBtn')?.addEventListener('click', function() {
    var date = document.getElementById('attendanceDate').value;
    var url = window.location.pathname + '?date=' + encodeURIComponent(date) + '&take=1';
    window.location.href = url;
});
document.getElementById('markAttendanceForm')?.addEventListener('submit', function(e) {
    var date = document.getElementById('attendanceDate').value;
    document.getElementById('markAttendanceDate').value = date;
});
let mealsHostelId = {{ $selectedHostel->id ?? 'null' }};
let mealsAttendanceDate = @json($date);
// Add Edit Attendance button handler if button exists
const editMealsAttendanceBtn = document.getElementById('editMealsAttendanceBtn');
if (editMealsAttendanceBtn) {
    editMealsAttendanceBtn.addEventListener('click', function(e) {
        e.preventDefault();
        // Date range validation: only allow editing for today and previous 4 days (using date strings to avoid timezone issues)
        const selectedDateStr = mealsAttendanceDate;
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
            const errorDiv = document.getElementById('edit-meals-attendance-error');
            errorDiv.innerHTML = '<div class="alert alert-danger mt-3">You cannot edit meals attendance for this date. Editing is only allowed for today and the previous 4 days.</div>';
            setTimeout(() => { errorDiv.innerHTML = ''; }, 4000);
            return;
        }
        // If valid, redirect to edit mode (or show edit form as per your app logic)
        window.location.href = `?date=${mealsAttendanceDate}&edit=1`;
    });
}
document.getElementById('applyDateBtn')?.addEventListener('click', function() {
    var date = document.getElementById('attendanceDate').value;
    var url = window.location.pathname + '?date=' + encodeURIComponent(date);
    window.location.href = url;
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