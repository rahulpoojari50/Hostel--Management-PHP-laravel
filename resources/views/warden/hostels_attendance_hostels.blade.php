@extends('layouts.admin')

@section('title', '')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800 mb-0">Select Hostel for Attendance</h1>
    </div>
    <div class="row">
        @forelse($hostels as $hostel)
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title">{{ $hostel->name }}</h5>
                        <p class="card-text">{{ $hostel->address }}</p>
                    </div>
                    <a href="{{ route('warden.hostels.attendance', $hostel->id) }}" class="btn btn-primary mt-3">Take Attendance</a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">No hostels assigned to you.</div>
        </div>
        @endforelse
    </div>
</div>
@endsection 