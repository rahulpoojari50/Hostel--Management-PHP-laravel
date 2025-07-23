@extends('layouts.admin')

@section('title', 'Registered Students')

@section('content')
<div class="container-fluid py-4">
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
                <small class="form-text text-muted">CSV columns: name, email, phone, address, hostel, room_type</small>
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

    @include('components.breadcrumb', [
        'pageTitle' => 'Registered Students',
        'breadcrumbs' => [
            ['name' => 'Home', 'url' => url('/')],
            ['name' => 'Hostels Management', 'url' => route('warden.hostels.index')],
            ['name' => 'Registered Students', 'url' => '']
        ]
    ])
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
                            <th>Email</th>
                            <th>Room Type</th>
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
                                <td>{{ $student->name ?? '-' }}</td>
                                <td>{{ $student->email ?? '-' }}</td>
                                <td>{{ $app->roomType->type ?? '-' }}</td>
                                <td>{{ $assignment?->room->room_number ?? 'Pending Allotment' }}</td>
                                <td>
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