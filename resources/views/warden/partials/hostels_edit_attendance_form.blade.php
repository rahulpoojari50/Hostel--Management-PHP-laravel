<h1 class="h3 mb-4 text-gray-800">Edit Attendance for {{ $hostel->name }}</h1>
<button type="button" class="btn btn-secondary mb-3" id="backToAttendanceBtn">&larr; Back</button>
<form method="POST" action="{{ route('warden.warden.hostels.attendance.store', $hostel->id) }}">
    @csrf
    <input type="hidden" name="date" value="{{ $date }}">
    <div class="mb-3">
        <strong>Date:</strong> {{ $date }}
    </div>
    <div class="mb-3">
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="mark_all" id="mark_all_present" value="Taken" onclick="markAll('Taken'); this.blur();">
            <label class="form-check-label" for="mark_all_present">All Present</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="mark_all" id="mark_all_absent" value="Skipped" onclick="markAll('Skipped'); this.blur();">
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
                            <th>USN</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        @php $record = $records->where('student_id', $student->id)->first(); @endphp
                                                        <tr>
                                    <td>
                                        <a href="#" class="student-name-clickable text-primary" data-student-id="{{ $student->id }}" style="text-decoration: none; cursor: pointer;">
                                            <i class="fas fa-user mr-1"></i>{{ $student->name }}
                                        </a>
                                    </td>
                            <td>{{ $student->usn ?? '-' }}</td>
                            <td class="text-center">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input status-radio" type="radio" name="status[{{ $student->id }}]" id="present_{{ $student->id }}" value="Taken" @if(optional($record)->status === 'Taken' || !$record) checked @endif onchange="updateSummary(); clearBulk();">
                                    <label class="form-check-label" for="present_{{ $student->id }}">Present</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input status-radio" type="radio" name="status[{{ $student->id }}]" id="absent_{{ $student->id }}" value="Skipped" @if(optional($record)->status === 'Skipped') checked @endif onchange="updateSummary(); clearBulk();">
                                    <label class="form-check-label" for="absent_{{ $student->id }}">Absent</label>
                                </div>
                            </td>
                            <td>
                                <input type="text" name="remarks[{{ $student->id }}]" class="form-control" placeholder="Optional remarks" value="{{ $record->remarks ?? '' }}">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <strong>Summary:</strong>
                <span id="presentCount">Present: 0</span> |
                <span id="absentCount">Absent: 0</span> |
                <span id="percentage">Attendance: 0%</span>
            </div>
            <button class="btn btn-primary mt-3">Update Attendance</button>
        </div>
    </div>
</form>
<script>
function markAll(status) {
    document.querySelectorAll('.status-radio').forEach(function(radio) {
        if (radio.value === status) radio.checked = true;
    });
    updateSummary();
}
function clearBulk() {
    document.querySelectorAll('input[name=mark_all]').forEach(function(radio) {
        radio.checked = false;
    });
}
function updateSummary() {
    let present = 0, absent = 0;
    document.querySelectorAll('input.status-radio:checked').forEach(function(radio) {
        if (radio.value === 'Taken') present++;
        else absent++;
    });
    let total = present + absent;
    let percent = total ? Math.round((present / total) * 100) : 0;
    document.getElementById('presentCount').textContent = 'Present: ' + present;
    document.getElementById('absentCount').textContent = 'Absent: ' + absent;
    document.getElementById('percentage').textContent = 'Attendance: ' + percent + '%';
}
document.addEventListener('DOMContentLoaded', updateSummary);
</script> 