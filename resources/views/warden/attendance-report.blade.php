@extends('layouts.admin')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <!-- Breadcrumb Navigation -->
        @include('components.breadcrumb-nav', [
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'url' => route('warden.dashboard')],
                ['name' => 'Attendance Report', 'url' => '']
            ]
        ])
    </div>
    <div>
        <a href="{{ route('warden.hostel-attendance.hostels') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
        </a>
    </div>
</div>

<!-- Page Title -->
<div class="mb-4">
    <h5 class="mb-0 text-gray-800">Attendance Report</h5>
</div>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">Attendance Report Filters</div>
        <div class="card-body">
            <form method="GET" action="{{ route('warden.attendance.report') }}">
                <div class="row mb-3">
                    <div class="col-md-4 mb-3">
                        <label>Attendance Type</label>
                        <select name="attendance_type" class="form-control">
                            <option value="">Select Type</option>
                            <option value="hostel" {{ request('attendance_type') == 'hostel' ? 'selected' : '' }}>Hostel Attendance</option>
                            <option value="meal" {{ request('attendance_type') == 'meal' ? 'selected' : '' }}>Meal Attendance</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter name" value="{{ request('name') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Hostel Name</label>
                        <select name="hostel_id" class="form-control">
                            <option value="">Select Hostel</option>
                            @foreach($hostels as $hostel)
                                <option value="{{ $hostel->id }}" {{ request('hostel_id') == $hostel->id ? 'selected' : '' }}>{{ $hostel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Room Type</label>
                        <select name="room_type" class="form-control">
                            <option value="">Select</option>
                            <option value="Single" {{ request('room_type') == 'Single' ? 'selected' : '' }}>Single</option>
                            <option value="Shared" {{ request('room_type') == 'Shared' ? 'selected' : '' }}>Shared</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter email" value="{{ request('email') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Room No</label>
                        <input type="text" name="room_no" class="form-control" placeholder="Enter room no" value="{{ request('room_no') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Category</label>
                        <select name="category" class="form-control">
                            <option value="">Select</option>
                            <option value="General" {{ request('category') == 'General' ? 'selected' : '' }}>General</option>
                            <option value="OBC" {{ request('category') == 'OBC' ? 'selected' : '' }}>OBC</option>
                            <option value="SC" {{ request('category') == 'SC' ? 'selected' : '' }}>SC</option>
                            <option value="ST" {{ request('category') == 'ST' ? 'selected' : '' }}>ST</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                </div>
                <button type="submit" class="btn btn-success">Search</button>
            </form>
        </div>
    </div>
</div>
@if(request('attendance_type') == 'meal')
    @if(isset($students) && isset($dates) && isset($mealAttendanceMatrix) && isset($selectedHostel))
        <div class="card mt-4">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <span>Meal Attendance Report</span>
                <div>
                    <a href="{{ route('warden.warden.meals-attendance.download-pdf', ['hostel' => $selectedHostel->id, 'date_from' => request('date_from'), 'date_to' => request('date_to')]) }}" class="btn btn-sm btn-danger mr-2"><i class="fas fa-file-pdf"></i> Download Meal Attendance PDF</a>
                    <a href="{{ route('warden.warden.warden.meals-attendance.download-csv-full', ['hostel' => $selectedHostel->id, 'date_from' => request('date_from'), 'date_to' => request('date_to')]) }}" class="btn btn-sm btn-success mr-2"><i class="fas fa-file-csv"></i> Download Meal Attendance CSV</a>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Name</th>
                            <th>USN</th>
                            <th>Email</th>
                            <th>Room</th>
                            <th>Hostel</th>
                            @foreach($dates as $date)
                                <th>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</th>
                            @endforeach
                            <th>Report</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $serial = 1; @endphp
                        @foreach($students as $student)
                                                <tr class="align-middle">
                        <td>{{ $serial++ }}</td>
                        <td>
                            <a href="#" class="student-name-clickable text-primary" data-student-id="{{ $student->id }}" style="text-decoration: none; cursor: pointer;">
                                <i class="fas fa-user mr-1"></i>{{ $student->name }}
                            </a>
                        </td>
                                <td>{{ $student->usn ?? '-' }}</td>
                                <td>{{ $student->email }}</td>
                                <td>{{ optional($student->roomAssignments->first()->room ?? null)->room_number }}</td>
                                <td>{{ optional($student->roomAssignments->first()->room->hostel ?? null)->name }}</td>
                                @foreach($dates as $date)
                                    @php
                                        $meals = $mealAttendanceMatrix[$student->id][$date] ?? [];
                                        $mealTypes = ['Breakfast', 'Lunch', 'Snacks', 'Dinner'];
                                    @endphp
                                    <td>
                                        @foreach($mealTypes as $abbr => $mealType)
                                            @php $short = substr($mealType,0,1); $status = $meals[$mealType] ?? null; @endphp
                                            @if($status === 'Taken')
                                                <span class="badge badge-success mx-1">{{ $short }}-P</span>
                                            @elseif($status === 'Skipped')
                                                <span class="badge badge-danger mx-1">{{ $short }}-A</span>
                                            @elseif($status === 'On Leave')
                                                <span class="badge badge-warning mx-1">{{ $short }}-L</span>
                                            @elseif($status === 'Holiday')
                                                <span class="badge badge-info mx-1">{{ $short }}-H</span>
                                            @else
                                                <span class="badge badge-secondary mx-1">{{ $short }}-N</span>
                                            @endif
                                        @endforeach
                                    </td>
                                @endforeach
                                <td>
                                    @php $summary = $mealAttendanceSummary[$student->id] ?? ['present'=>0,'total'=>0]; @endphp
                                    @if($summary['total'] > 0)
                                        <span class="font-weight-bold">{{ $summary['present'] }}/{{ $summary['total'] }} meals taken</span><br>
                                        <span class="text-muted small">{{ round(($summary['present']/$summary['total'])*100) }}% present</span>
                                    @else
                                        <span class="text-muted">No meals marked</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@else
    @if(request()->has('search') && (!request('hostel_id') || !request('date_from') || !request('date_to')))
        <div class="alert alert-warning mt-4">Please select a hostel and date range to view attendance status.</div>
    @endif
    @if(isset($students) && (!isset($dates) || !count($dates)))
        <div class="card mt-4">
            <div class="card-header bg-info text-white">Student List</div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>USN</th>
                            <th>Room Number</th>
                            <th>Email</th>
                            <th>Hostel Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($students))
                            @foreach($students as $student)
                                                        <tr>
                            <td>
                                <a href="#" class="student-name-clickable text-primary" data-student-id="{{ $student->id }}" style="text-decoration: none; cursor: pointer;">
                                    <i class="fas fa-user mr-1"></i>{{ $student->name }}
                                </a>
                            </td>
                                    <td>{{ $student->usn ?? '-' }}</td>
                                    <td>{{ optional($student->roomAssignments->first()->room ?? null)->room_number }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>{{ optional($student->roomAssignments->first()->room->hostel ?? null)->name }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="text-center text-muted">No students found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endif
    @if(isset($students) && isset($dates) && count($dates))
        <div class="card mt-4">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <span>Attendance Report</span>
                <div>
                    <a href="{{ request()->fullUrlWithQuery(['download' => 'csv']) }}" class="btn btn-sm btn-light mr-2"><i class="fas fa-file-csv"></i> Download CSV</a>
                    <a href="{{ request()->fullUrlWithQuery(['download' => 'pdf']) }}" class="btn btn-sm btn-light"><i class="fas fa-file-pdf"></i> Download PDF</a>
                </div>
            </div>
            <div class="table-responsive attendance-scroll">
                <table class="table table-bordered table-striped attendance-table">
                    <thead>
                        <tr>
                            <th class="sticky-col sticky-header" style="left:0;">Name</th>
                            <th class="sticky-col sticky-header" style="left:120px;">Room Number</th>
                            <th class="sticky-col sticky-header" style="left:240px;">Email</th>
                            <th class="sticky-col sticky-header" style="left:360px;">Hostel Name</th>
                            @foreach($dates as $date)
                                <th class="sticky-header">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</th>
                            @endforeach
                            <th class="sticky-header">Total Present</th>
                            <th class="sticky-header">Total Absent</th>
                            <th class="sticky-header">Total On Leave</th>
                            <th class="sticky-header">Total Holiday</th>
                            <th class="sticky-header">Attendance %</th>
                            <th class="sticky-header">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($students))
                            @foreach($students as $student)
                                @php
                                    $present = 0;
                                    $absent = 0;
                                    $onLeave = 0;
                                    $holiday = 0;
                                    foreach($dates as $date) {
                                        $status = $attendanceData[$student->id][$date] ?? null;
                                        if($status === 'Taken') $present++;
                                        elseif($status === 'Skipped') $absent++;
                                        elseif($status === 'On Leave') $onLeave++;
                                        elseif($status === 'Holiday') $holiday++;
                                    }
                                    $total = count($dates);
                                    $percent = $total ? round(($present / $total) * 100) : 0;
                                    if($percent >= 90) {
                                        $statusText = 'Excellent'; $badge = 'success';
                                    } elseif($percent >= 75) {
                                        $statusText = 'Good'; $badge = 'warning';
                                    } else {
                                        $statusText = 'Poor'; $badge = 'danger';
                                    }
                                @endphp
                                <tr>
                                    <td class="sticky-col" style="left:0; background:#fff;">
                                        <a href="#" class="student-name-clickable text-primary" data-student-id="{{ $student->id }}" style="text-decoration: none; cursor: pointer;">
                                            <i class="fas fa-user mr-1"></i>{{ $student->name }}
                                        </a>
                                    </td>
                                    <td class="sticky-col" style="left:120px; background:#fff;">{{ optional($student->roomAssignments->first()->room ?? null)->room_number }}</td>
                                    <td class="sticky-col" style="left:240px; background:#fff;">{{ $student->email }}</td>
                                    <td class="sticky-col" style="left:360px; background:#fff;">{{ optional($student->roomAssignments->first()->room->hostel ?? null)->name }}</td>
                                    @foreach($dates as $date)
                                        @php $status = $attendanceData[$student->id][$date] ?? null; @endphp
                                        <td>
                                            @if($status === 'Taken')
                                                <span class="badge badge-success">Present</span>
                                            @elseif($status === 'Skipped')
                                                <span class="badge badge-danger">Absent</span>
                                            @elseif($status === 'On Leave')
                                                <span class="badge badge-warning">On Leave</span>
                                            @elseif($status === 'Holiday')
                                                <span class="badge badge-info">Holiday</span>
                                            @elseif($status)
                                                <span class="badge badge-secondary">{{ $status }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td><span class="badge badge-success">{{ $present }}</span></td>
                                    <td><span class="badge badge-danger">{{ $absent }}</span></td>
                                    <td><span class="badge badge-warning">{{ $onLeave }}</span></td>
                                    <td><span class="badge badge-info">{{ $holiday }}</span></td>
                                    <td><span class="badge badge-info">{{ $percent }}%</span></td>
                                    <td><span class="badge badge-{{ $badge }}">{{ $statusText }}</span></td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="{{ 10 + count($dates) }}" class="text-center text-muted">No students found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endif

@include('components.student-profile-modal')
@endsection 

<style>
.attendance-scroll { overflow-x: auto; }
.attendance-table { min-width: 1200px; }
.sticky-header { position: sticky; top: 0; z-index: 2; background: #f8f9fa !important; }
.sticky-col { position: sticky; z-index: 1; background: #fff !important; }
.attendance-table th, .attendance-table td { white-space: nowrap; }
.table-hover tbody tr:hover {
    background-color: #f1f7ff;
}
.table-striped tbody tr:nth-of-type(odd) {
    background-color: #f9f9f9;
}
.badge-success { background-color: #28a745 !important; color: #fff; }
.badge-danger { background-color: #dc3545 !important; color: #fff; }
.badge-warning { background-color: #ffc107 !important; color: #212529; }
.badge-info { background-color: #17a2b8 !important; color: #fff; }
.badge-secondary { background-color: #6c757d !important; color: #fff; }
</style> 