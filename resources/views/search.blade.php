@extends('layouts.admin')

@section('title', 'Search Results')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <!-- Breadcrumb Navigation -->
        @include('components.breadcrumb-nav', [
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => url('/')],
                ['name' => 'Search Results', 'url' => '']
            ]
        ])
    </div>
    <div>
        {{-- Action buttons can go here --}}
    </div>
</div>

<!-- Page Title -->
<div class="mb-4">
    <h5 class="mb-0 text-gray-800">Search Results</h5>
</div>

<div class="container-fluid">
    <h4 class="mb-4">Search Results for: <span class="text-primary">{{ $q }}</span></h4>
    @if(!$q)
        <div class="alert alert-info">Enter a search term above.</div>
    @endif
    @if($q)
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Students</h6></div>
                    <div class="card-body">
                        @forelse($students as $student)
                            <div class="mb-2">
                                <strong>{{ $student->name }}</strong> ({{ $student->email }})<br>
                                USN: {{ $student->usn ?? '-' }}<br>
                                @php
                                    $assignment = $student->roomAssignments->where('status', 'active')->first();
                                    $hostelName = $assignment && $assignment->room && $assignment->room->hostel ? $assignment->room->hostel->name : '-';
                                @endphp
                                Hostel: {{ $hostelName }}<br>
                                <a href="{{ route('warden.students.edit', $student->id) }}" class="btn btn-sm btn-info mt-1">View</a>
                            </div>
                        @empty
                            <span class="text-muted">No students found.</span>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Hostels</h6></div>
                    <div class="card-body">
                        @forelse($hostels as $hostel)
                            <div class="mb-2">
                                <strong>{{ $hostel->name }}</strong> ({{ ucfirst($hostel->type) }})<br>
                                Address: {{ $hostel->address }}<br>
                                <a href="{{ route('warden.manage-hostel.show', $hostel->id) }}" class="btn btn-sm btn-info mt-1">View</a>
                            </div>
                        @empty
                            <span class="text-muted">No hostels found.</span>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Rooms</h6></div>
                    <div class="card-body">
                        @forelse($rooms as $room)
                            <div class="mb-2">
                                <strong>Room {{ $room->room_number }}</strong><br>
                                Hostel: {{ $room->hostel->name ?? '-' }}<br>
                                <a href="#" class="btn btn-sm btn-info mt-1 disabled">View</a>
                            </div>
                        @empty
                            <span class="text-muted">No rooms found.</span>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Fees</h6></div>
                    <div class="card-body">
                        @forelse($fees as $fee)
                            <div class="mb-2">
                                <strong>{{ ucwords(str_replace('_', ' ', $fee->fee_type)) }}</strong>: ₹{{ number_format($fee->amount, 2) }}<br>
                                Student: {{ $fee->student->name ?? '-' }}<br>
                            </div>
                        @empty
                            <span class="text-muted">No fees found.</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection 