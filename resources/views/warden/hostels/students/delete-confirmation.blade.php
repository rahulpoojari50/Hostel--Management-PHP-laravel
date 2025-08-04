@extends('layouts.admin')

@section('title', 'Confirm Student Deletion')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <!-- Breadcrumb Navigation -->
        @include('components.breadcrumb-nav', [
            'breadcrumbs' => [
                ['name' => 'Hostel Dashboard', 'url' => url('/warden/dashboard')],
                ['name' => 'Hostel Students', 'url' => route('warden.hostels.students', $hostel->id)],
                ['name' => 'Confirm Deletion', 'url' => '']
            ]
        ])
    </div>
</div>

<!-- Page Title -->
<div class="mb-4">
    <h5 class="mb-0 text-gray-800">Confirm Student Deletion</h5>
</div>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Confirm Student Deletion
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle"></i> 
                            Warning: This action cannot be undone!
                        </h6>
                        <p class="mb-0">
                            You are about to permanently delete <strong>{{ count($students) }} student(s)</strong> from 
                            <strong>{{ $hostel->name }}</strong>. This action will:
                        </p>
                        <ul class="mb-0 mt-2">
                            <li>Permanently delete the student accounts</li>
                            <li>Remove all room applications for these students</li>
                            <li>Delete all associated data (attendance, fees, etc.)</li>
                            <li>This action cannot be reversed</li>
                        </ul>
                    </div>

                    <h6 class="font-weight-bold mb-3">Students to be deleted:</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Name</th>
                                    <th>USN</th>
                                    <th>Email</th>
                                    <th>Room Assignment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                    @php
                                        $assignment = $student->roomAssignments()->where('status', 'active')->with('room')->first();
                                    @endphp
                                    <tr>
                                        <td>
                                            <i class="fas fa-user mr-1"></i>
                                            {{ $student->name }}
                                        </td>
                                        <td>{{ $student->usn ?? 'N/A' }}</td>
                                        <td>{{ $student->email }}</td>
                                        <td>
                                            @if($assignment && $assignment->room)
                                                <span class="badge badge-info">
                                                    {{ $assignment->room->room_number }}
                                                </span>
                                            @else
                                                <span class="badge badge-warning">No Room Assigned</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <form method="POST" action="{{ route('warden.hostels.students.delete', $hostel->id) }}">
                            @csrf
                            @method('DELETE')
                            
                            @foreach($studentIds as $studentId)
                                <input type="hidden" name="student_ids[]" value="{{ $studentId }}">
                            @endforeach
                            <input type="hidden" name="confirmed" value="true">
                            
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('warden.hostels.students', $hostel->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> 
                                    Confirm Deletion of {{ count($students) }} Student(s)
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 