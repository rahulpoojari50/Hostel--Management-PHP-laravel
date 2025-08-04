@extends('layouts.admin')

@section('title', 'Add Hostel')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <!-- Breadcrumb Navigation -->
        @include('components.breadcrumb-nav', [
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'url' => route('warden.dashboard')],
                ['name' => 'Home', 'url' => route('warden.dashboard')],
                ['name' => 'Add Hostel', 'url' => '']
            ]
        ])
    </div>
    <div>
        {{-- Action buttons can go here --}}
    </div>
</div>

<!-- Page Title -->
<div class="mb-4">
    <h5 class="mb-0 text-gray-800">Add Hostel</h5>
</div>

<div class="container-fluid py-4">
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('warden.hostels.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Type</label>
                    <select name="type" class="form-control" required>
                        <option value="boys">Boys</option>
                        <option value="girls">Girls</option>
                        <option value="mixed">Mixed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Create Hostel</button>
                <a href="{{ route('warden.hostels.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection 