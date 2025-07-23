@extends('layouts.admin')

@section('title', 'Manage Hostel')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    {{-- <h1 class="h3 mb-0 text-gray-800">Manage Hostel</h1> --}}
</div>

@include('components.breadcrumb', [
    'pageTitle' => 'Manage Hostel',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'Manage Hostel', 'url' => '']
    ]
])

<!-- Content Row -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Select a Hostel to Manage</h6>
            </div>
            <div class="card-body">
                @if($hostels->count() > 0)
                    <div class="row">
                        @foreach($hostels as $hostel)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card border-left-primary shadow h-100">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    {{ $hostel->name }}
                                                </div>
                                                <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                    {{ $hostel->type }} Hostel
                                                </div>
                                                <div class="text-muted small">
                                                    {{ Str::limit($hostel->address, 50) }}
                                                </div>
                                                <div class="mt-3">
                                                    <a href="{{ route('warden.manage-hostel.show', $hostel) }}" 
                                                       class="btn btn-primary btn-sm">
                                                        <i class="fas fa-cogs fa-sm"></i> Manage
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-building fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted">
                        <i class="fas fa-building fa-3x mb-3"></i>
                        <h5>No Hostels Available</h5>
                        <p>You don't have any hostels assigned to manage.</p>
                        <a href="{{ route('warden.hostels.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus fa-sm"></i> Create Hostel
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 