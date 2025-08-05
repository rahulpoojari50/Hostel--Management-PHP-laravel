@extends('layouts.admin')

@section('title', 'Select Hostel')

@section('content')
<div class="container-fluid py-4">
    @include('components.breadcrumb', [
        'pageTitle' => 'Select Hostel',
        'breadcrumbs' => [
            ['name' => 'Dashboard', 'url' => url('/')],
            ['name' => 'Select Hostel', 'url' => '']
        ]
    ])
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Select a Hostel to {{ $action === 'rooms' ? 'Manage Rooms' : 'Manage Meals' }}
                    </h6>
                </div>
                <div class="card-body">
                    @if($hostels->count())
                        <div class="list-group">
                            @foreach($hostels as $hostel)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $hostel->name }}</strong>
                                        <div class="text-muted small">{{ Str::limit($hostel->description, 60) }}</div>
                                    </div>
                                    <div>
                                        @if($action === 'rooms')
                                            <a href="{{ route('warden.rooms.show', $hostel) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-bed"></i> Manage Rooms
                                            </a>
                                        @else
                                            <a href="{{ route('warden.manage-hostel.show', $hostel) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-cogs"></i> Manage Hostel
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning">No hostels found.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 