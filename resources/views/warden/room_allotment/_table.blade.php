<table class="table table-bordered w-100 mb-0">
    <thead class="thead-light">
        <tr>
            <th><input type="checkbox" id="selectAllReject"></th>
            <th>Student Name</th>
            <th>USN</th>
            <th>Email</th>
            <th>Hostel</th>
            <th>Room Type</th>
            <th>Applied Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pendingApplications as $application)
            <tr>
                <td><input type="checkbox" name="application_ids[]" value="{{ $application->id }}"></td>
                <td>
                    @if($application->student)
                        <a href="#" class="student-name-clickable text-primary" data-student-id="{{ $application->student->id }}" style="text-decoration: none; cursor: pointer;">
                            <i class="fas fa-user mr-1"></i>{{ $application->student->name }}
                        </a>
                    @else
                        Unknown Student
                    @endif
                </td>
                <td>{{ $application->student->usn ?? '-' }}</td>
                <td>{{ $application->student->email ?? '-' }}</td>
                <td>{{ $application->hostel->name ?? 'Unknown Hostel' }}</td>
                <td>{{ $application->roomType->type ?? 'Unknown Room Type' }}</td>
                <td>{{ $application->created_at->format('M d, Y') }}</td>
                <td>
                    <a href="{{ route('warden.room-allotment.show', $application) }}" 
                       class="btn btn-primary btn-sm">
                        <i class="fas fa-user-plus fa-sm"></i> Allot Room
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<div class="d-flex justify-content-end">
    <!-- Pagination removed for debugging -->
</div> 