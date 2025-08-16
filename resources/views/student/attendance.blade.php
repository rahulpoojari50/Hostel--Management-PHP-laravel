@extends('layouts.admin')

@section('title', 'Attendance History')

@section('content')
<div class="container-fluid py-4">
    <h1 class="h3 mb-4 text-gray-800 ">Attendance History</h1>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Meal Attendance</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $mealAttendancePercentage }}%</div>
                            <small class="text-muted">{{ $presentMealRecords }}/{{ $totalMealRecords }} records</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-utensils fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Hostel Attendance</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $hostelAttendancePercentage }}%</div>
                            <small class="text-muted">{{ $presentHostelRecords }}/{{ $totalHostelRecords }} records</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-home fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Records</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalMealRecords + $totalHostelRecords }}</div>
                            <small class="text-muted">Combined attendance</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Average Attendance</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $totalRecords = $totalMealRecords + $totalHostelRecords;
                                    $totalPresent = $presentMealRecords + $presentHostelRecords;
                                    $avgPercentage = $totalRecords > 0 ? round(($totalPresent / $totalRecords) * 100, 2) : 0;
                                @endphp
                                {{ $avgPercentage }}%
                            </div>
                            <small class="text-muted">Overall performance</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row">
                <div class="col-md-3">
                    <label>Date</label>
                    <input type="date" name="date" class="form-control" value="{{ $selectedDate ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label>Type</label>
                    <select name="type" class="form-control">
                        <option value="all" {{ $attendanceType == 'all' ? 'selected' : '' }}>All Attendance</option>
                        <option value="meal" {{ $attendanceType == 'meal' ? 'selected' : '' }}>Meal Attendance Only</option>
                        <option value="hostel" {{ $attendanceType == 'hostel' ? 'selected' : '' }}>Hostel Attendance Only</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Records per page</label>
                    <select name="per_page" class="form-control">
                        <option value="10" {{ request('per_page', 20) == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page', 20) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page', 20) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">Filter</button>
                    <a href="{{ route('student.attendance') }}" class="btn btn-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Attendance History</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Date</th>
                            <th>Meal Attendance</th>
                            <th>Hostel Attendance</th>
                            <th>Overall Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paginatedDates as $date)
                            @php
                                $mealData = $mealAttendanceByDate[$date] ?? [];
                                $hostelData = $hostelAttendanceByDate[$date] ?? null;
                                
                                // Calculate meal attendance status
                                $mealPresent = collect($mealData)->contains('Taken');
                                $mealAbsent = collect($mealData)->contains('Skipped');
                                $mealCount = count($mealData);
                                
                                // Calculate overall status
                                $overallStatus = 'Unknown';
                                if ($mealPresent && $hostelData && $hostelData['status'] == 'Taken') {
                                    $overallStatus = 'Present';
                                } elseif ($mealAbsent || ($hostelData && $hostelData['status'] == 'Skipped')) {
                                    $overallStatus = 'Absent';
                                } elseif ($hostelData && $hostelData['status'] == 'On Leave') {
                                    $overallStatus = 'On Leave';
                                } elseif ($hostelData && $hostelData['status'] == 'Holiday') {
                                    $overallStatus = 'Holiday';
                                }
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</strong><br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($date)->format('l') }}</small>
                                </td>
                                <td>
                                    @if($mealCount > 0)
                                        <div class="mb-2">
                                            @foreach($mealTypes as $mealType)
                                                @php
                                                    $status = $mealData[$mealType] ?? null;
                                                    $statusClass = $status == 'Taken' ? 'success' : ($status == 'Skipped' ? 'danger' : 'secondary');
                                                    $statusText = $status == 'Taken' ? 'P' : ($status == 'Skipped' ? 'A' : '-');
                                                @endphp
                                                <span class="badge badge-{{ $statusClass }} mr-1" title="{{ $mealType }}">{{ $statusText }}</span>
                                            @endforeach
                                        </div>
                                        <small class="text-muted">
                                            @if($mealPresent && $mealAbsent)
                                                Mixed
                                            @elseif($mealPresent)
                                                Present
                                            @elseif($mealAbsent)
                                                Absent
                                            @else
                                                No data
                                            @endif
                                        </small>
                                    @else
                                        <span class="text-muted">No meal records</span>
                                    @endif
                                </td>
                                <td>
                                    @if($hostelData)
                                        @php
                                            $hostelStatusClass = $hostelData['status'] == 'Taken' ? 'success' : 
                                                               ($hostelData['status'] == 'Skipped' ? 'danger' : 
                                                               ($hostelData['status'] == 'On Leave' ? 'warning' : 
                                                               ($hostelData['status'] == 'Holiday' ? 'info' : 'secondary')));
                                        @endphp
                                        <span class="badge badge-{{ $hostelStatusClass }} mb-2">
                                            {{ $hostelData['status'] }}
                                        </span>
                                        @if($hostelData['remarks'])
                                            <br><small class="text-muted">{{ $hostelData['remarks'] }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">No hostel record</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $overallClass = $overallStatus == 'Present' ? 'success' : 
                                                      ($overallStatus == 'Absent' ? 'danger' : 
                                                      ($overallStatus == 'On Leave' ? 'warning' : 
                                                      ($overallStatus == 'Holiday' ? 'info' : 'secondary')));
                                    @endphp
                                    <span class="badge badge-{{ $overallClass }}">{{ $overallStatus }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">
                                    @if($selectedDate)
                                        No attendance records found for this date.
                                    @else
                                        No attendance records found.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(count($allDates) > count($paginatedDates))
                <div class="d-flex justify-content-center mt-4">
                    <nav>
                        <ul class="pagination">
                            @php
                                $totalPages = ceil(count($allDates) / request('per_page', 20));
                                $currentPage = request('page', 1);
                            @endphp
                            
                            @if($currentPage > 1)
                                <li class="page-item">
                                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}">Previous</a>
                                </li>
                            @endif
                            
                            @for($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++)
                                <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">{{ $i }}</a>
                                </li>
                            @endfor
                            
                            @if($currentPage < $totalPages)
                                <li class="page-item">
                                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}">Next</a>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            @endif
        </div>
    </div>

    <!-- Legend -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Legend</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Meal Attendance:</h6>
                    <span class="badge badge-success mr-2">P</span> = Present (Taken)<br>
                    <span class="badge badge-danger mr-2">A</span> = Absent (Skipped)<br>
                    <span class="badge badge-secondary mr-2">-</span> = No Record
                </div>
                <div class="col-md-6">
                    <h6>Hostel Attendance:</h6>
                    <span class="badge badge-success mr-2">Taken</span> = Present<br>
                    <span class="badge badge-danger mr-2">Skipped</span> = Absent<br>
                    <span class="badge badge-warning mr-2">On Leave</span> = Leave<br>
                    <span class="badge badge-info mr-2">Holiday</span> = Holiday
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 