@extends('layouts.admin')

@section('title', 'Meal Details')

@section('content')
<div class="container-fluid py-4">
    @include('components.breadcrumb', [
        'pageTitle' => 'Meal Details',
        'breadcrumbs' => [
            ['name' => 'Home', 'url' => url('/')],
            ['name' => 'Meals', 'url' => route('warden.meals.index')],
            ['name' => 'Meal Details', 'url' => '']
        ]
    ])

    <!-- Meal Information Card -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-utensils"></i> Meal Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Hostel:</strong></td>
                                    <td>{{ $meal->hostel->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Meal Type:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $meal->meal_type === 'breakfast' ? 'info' : ($meal->meal_type === 'lunch' ? 'success' : ($meal->meal_type === 'dinner' ? 'primary' : 'warning')) }}">
                                            {{ ucfirst($meal->meal_type) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Date:</strong></td>
                                    <td>
                                        <strong>{{ \Carbon\Carbon::parse($meal->meal_date)->format('d M Y') }}</strong><br>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($meal->meal_date)->format('l') }}</small>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Menu Description:</strong></td>
                                    <td>
                                        @if($meal->menu_description)
                                            {{ $meal->menu_description }}
                                        @else
                                            <span class="text-muted">No menu description provided</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $meal->created_at->format('d M Y, h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Updated:</strong></td>
                                    <td>{{ $meal->updated_at->format('d M Y, h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Present Students
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $presentCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Absent Students
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $absentCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
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
                                Total Students
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Attendance Rate
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $attendancePercent }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Management -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clipboard-list"></i> Attendance Management
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('warden.meals.update', $meal) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="table-responsive">
                            <table class="table table-bordered" id="attendanceTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>USN</th>
                                        <th>Email</th>
                                        <th>Current Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $student)
                                                                        <tr>
                                    <td>
                                        <a href="#" class="student-name-clickable text-primary" data-student-id="{{ $student->id }}" style="text-decoration: none; cursor: pointer;">
                                            <i class="fas fa-user mr-1"></i><strong>{{ $student->name }}</strong>
                                        </a>
                                    </td>
                                            <td>{{ $student->usn ?? '-' }}</td>
                                            <td>{{ $student->email }}</td>
                                            <td>
                                                @php
                                                    $status = $attendance[$student->id] ?? 'absent';
                                                    $statusClass = $status === 'present' ? 'success' : 'danger';
                                                    $statusText = ucfirst($status);
                                                @endphp
                                                <span class="badge badge-{{ $statusClass }}">{{ $statusText }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="submit" 
                                                            name="attendance[{{ $student->id }}]" 
                                                            value="present" 
                                                            class="btn btn-success btn-sm"
                                                            title="Mark Present">
                                                        <i class="fas fa-check"></i> Present
                                                    </button>
                                                    <button type="submit" 
                                                            name="attendance[{{ $student->id }}]" 
                                                            value="absent" 
                                                            class="btn btn-danger btn-sm"
                                                            title="Mark Absent">
                                                        <i class="fas fa-times"></i> Absent
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-body">
                    <a href="{{ route('warden.meals.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Meals
                    </a>
                    <a href="{{ route('warden.meals.edit', $meal) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Meal
                    </a>
                    <form action="{{ route('warden.meals.destroy', $meal) }}" 
                          method="POST" 
                          class="d-inline"
                          onsubmit="return confirm('Are you sure you want to delete this meal?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Meal
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#attendanceTable').DataTable({
        "pageLength": 25,
        "order": [[0, "asc"]], // Sort by student name
        "language": {
            "search": "Search students:",
            "lengthMenu": "Show _MENU_ students per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ students"
        }
    });
});
</script>
@endpush

@include('components.student-profile-modal')
@endsection 