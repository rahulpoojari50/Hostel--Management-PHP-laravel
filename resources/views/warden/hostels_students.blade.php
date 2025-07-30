@extends('layouts.admin')

@section('title', 'Registered Students')

@section('content')
@php
    $hostels = $allHostels;
    $selectedHostelId = request('hostel_id') ?? ($hostels->count() === 1 ? $hostels->first()->id : $hostel->id ?? null);
    $selectedHostel = $hostels->where('id', $selectedHostelId)->first();
@endphp
<div class="container-fluid py-4">
    @include('components.breadcrumb', [
        'pageTitle' => 'Registered Students',
        'breadcrumbs' => [
            ['name' => 'Home', 'url' => url('/')],
            ['name' => 'Hostels Management', 'url' => route('warden.hostels.index')],
            ['name' => 'Registered Students', 'url' => '']
        ]
    ])
    {{-- Remove the top Add Student and Bulk Upload Buttons --}}
    {{-- Add Student Modal --}}
    <div class="modal fade" id="addStudentModal" tabindex="-1" role="dialog" aria-labelledby="addStudentModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form method="POST" action="{{ route('warden.hostels.students.add', $hostel->id) }}">
            @csrf
            <div class="modal-header">
              <h5 class="modal-title" id="addStudentModalLabel">Add Student</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label for="student_name">Name</label>
                <input type="text" class="form-control" id="student_name" name="name" required>
              </div>
              <div class="form-group">
                <label for="student_email">Email</label>
                <input type="email" class="form-control" id="student_email" name="email" required>
              </div>
              <div class="form-group">
                <label for="student_usn">USN</label>
                <input type="text" class="form-control" id="student_usn" name="usn" required>
              </div>
              <div class="form-group">
                <label for="student_phone">Phone</label>
                <input type="text" class="form-control" id="student_phone" name="phone">
              </div>
              <div class="form-group">
                <label for="student_address">Address</label>
                <input type="text" class="form-control" id="student_address" name="address">
              </div>
              <div class="form-group">
                <label for="student_hostel">Hostel</label>
                <select class="form-control" id="student_hostel" name="hostel_id" required>
                    <option value="">Select Hostel</option>
                    @foreach($allHostels as $h)
                        <option value="{{ $h->id }}" {{ $h->id == $hostel->id ? 'selected' : '' }}>{{ $h->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="student_room_type">Room Type</label>
                <select class="form-control" id="student_room_type" name="room_type_id" required>
                    <option value="">Select Room Type</option>
                    @foreach($hostel->roomTypes as $rt)
                        <option value="{{ $rt->id }}">{{ $rt->type }} ({{ $rt->capacity }} beds)</option>
                    @endforeach
                </select>
            </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Add Student</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    {{-- Bulk Upload Modal --}}
    <div class="modal fade" id="bulkUploadModal" tabindex="-1" role="dialog" aria-labelledby="bulkUploadModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form method="POST" action="{{ route('warden.hostels.students.bulk_upload', $hostel->id) }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
              <h5 class="modal-title" id="bulkUploadModalLabel">Bulk Upload Students (CSV)</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label for="csv_file">CSV File</label>
                <input type="file" class="form-control-file" id="csv_file" name="csv_file" accept=".csv" required>
                <small class="form-text text-muted">CSV columns: name, email, usn, phone, address, hostel, room_type</small>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-success">Upload</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    {{-- <h1 class="h3 mb-4 text-gray-800">Registered Students for Hostel: {{ $hostel->name }}</h1> --}}

    {{-- Student Search Filters --}}
    <div class="card p-4 mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Student Search Filters</h5>
      </div>
      <form method="GET" action="" autocomplete="off">
        <div class="row">
          <div class="col-md-4 mb-3">
            <label for="hostel_id">Select Hostel</label>
            <select class="form-control" id="hostel_id" name="hostel_id" required onchange="this.form.submit()">
              <option value="">-- Select Hostel --</option>
              @foreach($hostels as $h)
                <option value="{{ $h->id }}" {{ $selectedHostelId == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
              @endforeach
            </select>
          </div>
          @if($selectedHostel)
          <div class="col-md-4 mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" placeholder="Enter Name" value="{{ request('name') }}">
          </div>
          <div class="col-md-4 mb-3">
            <label>Room Type</label>
            <select name="room_type" class="form-control">
              <option value="">Select Room Type</option>
              @if($selectedHostel->roomTypes)
                @foreach($selectedHostel->roomTypes as $rt)
                  <option value="{{ $rt->type }}" {{ request('room_type') == $rt->type ? 'selected' : '' }}>{{ $rt->type }}</option>
                @endforeach
              @endif
            </select>
          </div>
          <div class="col-md-4 mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" placeholder="Enter Email" value="{{ request('email') }}">
          </div>
          <div class="col-md-4 mb-3">
            <label>Room No</label>
            <input name="room_no" list="room_no_list" class="form-control" placeholder="Enter or select Room No" value="{{ request('room_no') }}">
            <datalist id="room_no_list">
              <option value="none">None</option>
              @if($selectedHostel->roomApplications)
                @php
                  $roomNumbers = $selectedHostel->roomApplications->flatMap(function($app) use ($selectedHostel) {
                    return optional($app->student)->roomAssignments->where('room.hostel_id', $selectedHostel->id)->pluck('room.room_number');
                  })->unique()->filter();
                @endphp
                @foreach($roomNumbers as $roomNo)
                  <option value="{{ $roomNo }}">{{ $roomNo }}</option>
                @endforeach
              @endif
            </datalist>
          </div>
          <div class="col-md-4 mb-3">
            <label>Category</label>
            <select name="category" class="form-control">
              <option value="">Select Category</option>
              <option value="General" {{ request('category') == 'General' ? 'selected' : '' }}>General</option>
              <option value="OBC" {{ request('category') == 'OBC' ? 'selected' : '' }}>OBC</option>
              <option value="SC" {{ request('category') == 'SC' ? 'selected' : '' }}>SC</option>
              <option value="ST" {{ request('category') == 'ST' ? 'selected' : '' }}>ST</option>
              <option value="Other" {{ request('category') == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
          </div>
          @endif
          <div class="col-md-4 d-flex align-items-end mb-3">
            <button type="submit" class="btn btn-primary w-100">Search</button>
          </div>
        </div>
      </form>
    </div>
    {{-- END Student Search Filters --}}

    <form method="POST" action="{{ route('warden.hostels.students.delete', $hostel->id) }}" id="deleteStudentsForm">
        @csrf
        @method('DELETE')
        <div class="mb-3 d-flex justify-content-end align-items-center">
            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete the selected students?')">
                <i class="fas fa-trash"></i> Delete Selected
            </button>
        </div>
        <div class="card shadow mb-4">
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>Name</th>
                            <th>USN</th>
                            <th>Email</th>
                            <th>Room Type</th>
                            <th>Hostel Name</th>
                            <th>Room No</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $app)
                            @php
                                $student = $app->student;
                                $assignment = $student?->roomAssignments->where('room.hostel_id', $hostel->id)->first();
                            @endphp
                                                <tr>
                        <td><input type="checkbox" name="student_ids[]" value="{{ $student->id }}"></td>
                        <td>
                            @if($student)
                                <a href="#" class="student-name-clickable text-primary" data-student-id="{{ $student->id }}" style="text-decoration: none; cursor: pointer;">
                                    <i class="fas fa-user mr-1"></i>{{ $student->name }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                                <td>{{ $student->usn ?? '-' }}</td>
                                <td>{{ $student->email ?? '-' }}</td>
                                <td>{{ $app->roomType->type ?? '-' }}</td>
                                <td>{{ $hostel->name }}</td>
                                <td>{{ $assignment?->room->room_number ?? 'Pending Allotment' }}</td>
                                <td>
                                    <a href="{{ route('warden.students.show', $student->id) }}" class="btn btn-sm btn-info" title="View Profile">
                                        <i class="fas fa-eye"></i> Profile
                                    </a>
                                    <a href="{{ route('warden.students.edit', $student->id) }}" class="btn btn-sm btn-warning" title="Edit Student">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">No students registered.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>
    <script>
        document.getElementById('selectAll').addEventListener('change', function() {
            const checked = this.checked;
            document.querySelectorAll('input[name="student_ids[]"]').forEach(cb => cb.checked = checked);
        });
    </script>
    <div class="mb-3 d-flex justify-content-end align-items-center">
        <button class="btn btn-primary" data-toggle="modal" data-target="#addStudentModal">
            <i class="fas fa-user-plus"></i> Add Student
        </button>
        <button class="btn btn-success ml-2" data-toggle="modal" data-target="#bulkUploadModal">
            <i class="fas fa-file-upload"></i> Bulk Upload (CSV)
        </button>
    </div>
    <div class="d-flex justify-content-end">
        {{ $applications->links('pagination::bootstrap-4') }}
    </div>
    <a href="{{ route('warden.dashboard') }}" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>
@endsection
@push('scripts')
<script>
    const allRoomTypes = @json($allHostels->mapWithKeys(fn($h) => [$h->id => $h->roomTypes->map(fn($rt) => ['id' => $rt->id, 'type' => $rt->type, 'capacity' => $rt->capacity])->values()]))
    $(document).ready(function() {
        $('#student_hostel').on('change', function() {
            const hostelId = $(this).val();
            const roomTypes = allRoomTypes[hostelId] || [];
            const $roomType = $('#student_room_type');
            $roomType.empty().append('<option value="">Select Room Type</option>');
            roomTypes.forEach(rt => {
                $roomType.append(`<option value="${rt.id}">${rt.type} (${rt.capacity} beds)</option>`);
            });
        });
    });
</script>
@endpush

@include('components.student-profile-modal') 