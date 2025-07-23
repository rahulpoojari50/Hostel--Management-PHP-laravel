@extends('layouts.admin')

@section('title', 'Attendance')

@section('content')
<div class="container-fluid py-4">
    <h1 class="h3 mb-4 text-gray-800">Attendance</h1>
    <form method="GET" class="mb-4">
        <div class="form-row align-items-end">
            <div class="col-auto">
                <label>Date</label>
                <input type="date" name="date" class="form-control" value="{{ $selectedDate ?? '' }}" onchange="this.form.submit()">
            </div>
            @if($selectedDate)
            <div class="col-auto">
                <a href="{{ route('student.attendance') }}" class="btn btn-secondary">Clear</a>
            </div>
            @endif
        </div>
    </form>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Attendance</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendanceByDate as $date => $meals)
                            <tr>
                                <td>{{ $date }}</td>
                                @php
                                    $present = collect($meals)->contains('Taken');
                                @endphp
                                <td>
                                    @if($present)
                                        <span class="badge badge-success">Present</span>
                                    @else
                                        <span class="badge badge-danger">Absent</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center">
                                    @if($selectedDate)
                                        No attendance record found for this date.
                                    @else
                                        No attendance records found.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 