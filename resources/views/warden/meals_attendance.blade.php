@extends('layouts.admin')

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
    <h5 class="mb-0 text-gray-800">{{ $selectedHostel ? $selectedHostel->name . ' Meals Attendance' : 'Meals Attendance' }}</h5>
</div>

<div class="container-fluid">
    {{-- <h1 class="h3 mb-4 text-gray-800">Meals Attendance</h1> --}}
    @if($selectedHostel)
        {{-- <div class="mb-3">
            <h4>{{ $selectedHostel->name }}</h4>
        </div> --}}
    @endif
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
        <button type="button" class="btn btn-warning ml-2" id="editMealsAttendanceBtn">Edit Attendance</button>
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
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="mark_all" id="mark_all_leave" value="On Leave" onclick="markAllMeals('On Leave'); this.blur();">
                        <label class="form-check-label" for="mark_all_leave">All On Leave</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="mark_all" id="mark_all_holiday" value="Holiday" onclick="markAllMeals('Holiday'); this.blur();">
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
                                        <td>
                                            <a href="#" class="student-name-clickable text-primary" data-student-id="{{ $student->id }}" style="text-decoration: none; cursor: pointer;">
                                                <i class="fas fa-user mr-1"></i>{{ $student->name }}
                                            </a>
                                        </td>
                                        <td>{{ $student->usn ?? '-' }}</td>
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
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input status-radio" type="radio" name="status[{{ $student->id }}][{{ $meal }}]" value="On Leave" @if(isset($attendance[$meal][$student->id]) && $attendance[$meal][$student->id] == 'On Leave') checked @endif>
                                                <label class="form-check-label">L</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input status-radio" type="radio" name="status[{{ $student->id }}][{{ $meal }}]" value="Holiday" @if(isset($attendance[$meal][$student->id]) && $attendance[$meal][$student->id] == 'Holiday') checked @endif>
                                                <label class="form-check-label">H</label>
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
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="mark_all" id="mark_all_leave" value="On Leave" onclick="markAllMeals('On Leave'); this.blur();">
                        <label class="form-check-label" for="mark_all_leave">All On Leave</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="mark_all" id="mark_all_holiday" value="Holiday" onclick="markAllMeals('Holiday'); this.blur();">
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
                                        <td>
                                            <a href="#" class="student-name-clickable text-primary" data-student-id="{{ $student->id }}" style="text-decoration: none; cursor: pointer;">
                                                <i class="fas fa-user mr-1"></i>{{ $student->name }}
                                            </a>
                                        </td>
                                        <td>{{ $student->usn ?? '-' }}</td>
                                        @foreach(['Breakfast','Lunch','Snacks','Dinner'] as $meal)
                                        <td class="text-center">
                                            <div class="attendance-controls">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input status-radio" type="radio" name="status[{{ $student->id }}][{{ $meal }}]" value="Taken" checked @if($attendanceExists) disabled @endif>
                                                    <label class="form-check-label">P</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input status-radio" type="radio" name="status[{{ $student->id }}][{{ $meal }}]" value="Skipped" @if($attendanceExists) disabled @endif>
                                                    <label class="form-check-label">A</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input status-radio" type="radio" name="status[{{ $student->id }}][{{ $meal }}]" value="On Leave" @if($attendanceExists) disabled @endif>
                                                    <label class="form-check-label">L</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input status-radio" type="radio" name="status[{{ $student->id }}][{{ $meal }}]" value="Holiday" @if($attendanceExists) disabled @endif>
                                                    <label class="form-check-label">H</label>
                                                </div>
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
                                    <th>USN</th>
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
                                    <td>
                                        <a href="#" class="student-name-clickable text-primary" data-student-id="{{ $student->id }}" style="text-decoration: none; cursor: pointer;">
                                            <i class="fas fa-user mr-1"></i>{{ $student->name }}
                                        </a>
                                    </td>
                                    <td>{{ $student->usn ?? '-' }}</td>
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
document.addEventListener('DOMContentLoaded', function() {
    // Show modal on View Attendance click (all dates enabled)
    document.getElementById('viewAttendanceBtn')?.addEventListener('click', function(e) {
        e.preventDefault();
        modalAction = 'view';
        document.getElementById('modalAttendanceDate').removeAttribute('min');
        document.getElementById('modalAttendanceDate').removeAttribute('max');
        $('#dateModal').modal('show');
    });
    // Always open modal for Take Attendance
    document.getElementById('takeAttendanceBtn')?.addEventListener('click', function(e) {
        e.preventDefault();
        modalAction = 'take';
        var dateInput = document.getElementById('modalAttendanceDate');
        var today = new Date();
        var minDate = new Date();
        minDate.setDate(today.getDate() - 3);
        dateInput.setAttribute('max', formatDate(today));
        dateInput.setAttribute('min', formatDate(minDate));
        dateInput.value = '';
        $('#dateModal').modal('show');
    });
    // Show modal on Edit Attendance click (restrict dates)
    document.getElementById('editMealsAttendanceBtn')?.addEventListener('click', function(e) {
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
            var takeUrl = "/warden/meals-attendance/{{ $selectedHostel->id }}";
            takeUrl += '?date=' + encodeURIComponent(modalDate) + '&take=1';
            window.location.href = takeUrl;
        } else if (modalAction === 'edit') {
            // Redirect to edit attendance page for selected date
            var editUrl = "/warden/meals-attendance/{{ $selectedHostel->id }}";
            editUrl += '?date=' + encodeURIComponent(modalDate) + '&edit=1';
            window.location.href = editUrl;
        }
        $('#dateModal').modal('hide');
    });
});
function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();
    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;
    return [year, month, day].join('-');
}
</script>
@if(session('success'))
<script>
    setTimeout(() => {
        toastr.success("{{ session('success') }}");
    }, 500);
</script>
@endif

@include('components.student-profile-modal')

@push('styles')
<style>
.attendance-controls {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 2px;
    flex-wrap: nowrap;
}

.attendance-controls .form-check {
    margin: 0;
    padding: 0;
    min-width: auto;
}

.attendance-controls .form-check-input {
    width: 12px;
    height: 12px;
    margin: 0 2px 0 0;
}

.attendance-controls .form-check-label {
    font-size: 10px;
    font-weight: bold;
    margin: 0;
    padding: 0;
    line-height: 1;
}

.attendance-controls .form-check-inline {
    margin-right: 4px;
}
</style>
@endpush
@endsection 