@extends('layouts.admin')

@section('title', 'Add Hostel')

@section('content')
<div class="container-fluid py-4">
    <h1 class="h3 mb-4 text-gray-800">Add Hostel</h1>

    @include('components.breadcrumb', [
        'pageTitle' => 'Add Hostel',
        'breadcrumbs' => [
            ['name' => 'Home', 'url' => url('/')],
            ['name' => 'Hostels Management', 'url' => route('warden.hostels.index')],
            ['name' => 'Add Hostel', 'url' => '']
        ]
    ])
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