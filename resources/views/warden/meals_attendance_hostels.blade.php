@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Meals Attendance</h1>
    <div class="row">
        @forelse($hostels as $hostel)
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title">{{ $hostel->name }}</h5>
                        <p class="card-text">{{ $hostel->address }}</p>
                    </div>
                    <a href="{{ route('warden.meals-attendance.index', $hostel->id) }}" class="btn btn-primary mt-3">Take Attendance</a>
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