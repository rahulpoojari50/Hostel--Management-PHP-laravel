@extends('layouts.admin')

@section('title', 'Browse Hostel')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        @foreach($hostels as $hostel)
            <div class="col-md-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title mb-1">{{ $hostel->name }}</h5>
                            <div class="mb-2 text-muted">{{ $hostel->location ?? $hostel->address }}</div>
                            <div class="mb-2">
                                <strong>Room Types:</strong>
                                @foreach($hostel->roomTypes as $type)
                                    <span class="badge badge-info mr-1">{{ $type->type }} ({{ $type->rooms->count() }})</span>
                                @endforeach
                            </div>
                            <div class="mb-2"><strong>Rent:</strong> ₹
                                {{-- Show only hostel fees, not room rent --}}
                                @php
                                    $fees = method_exists($hostel, 'getTotalFeesForRoomType') ? $hostel->getTotalFeesForRoomType() : 0;
                                @endphp
                                {{ number_format($fees, 2) }}
                            </div>
                            <div class="mb-2">
                                <strong>Status:</strong>
                                @php
                                    $hasVacancy = $hostel->roomTypes->pluck('rooms')->flatten()->where('status', 'available')->count() > 0;
                                @endphp
                                <span class="badge badge-{{ $hasVacancy ? 'success' : 'danger' }}">{{ $hasVacancy ? 'Available' : 'Full' }}</span>
                            </div>
                        </div>
                        <a href="{{ route('student.hostels.show', $hostel->id) }}" class="btn btn-primary mt-2 w-100">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
