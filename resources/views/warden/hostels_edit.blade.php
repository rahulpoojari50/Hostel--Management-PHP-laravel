@extends('layouts.admin')

@section('title', 'Edit Hostel')

@section('content')
<div class="container-fluid py-4">
    <h1 class="h3 mb-4 text-gray-800">Edit Hostel</h1>

    @include('components.breadcrumb', [
        'pageTitle' => 'Edit Hostel',
        'breadcrumbs' => [
            ['name' => 'Home', 'url' => url('/')],
            ['name' => 'Hostels Management', 'url' => route('warden.hostels.index')],
            ['name' => 'Edit Hostel', 'url' => '']
        ]
    ])
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('warden.hostels.update', $hostel) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $hostel->name }}" required>
                </div>
                <div class="form-group">
                    <label>Type</label>
                    <select name="type" class="form-control" required>
                        <option value="boys" @if($hostel->type=='boys') selected @endif>Boys</option>
                        <option value="girls" @if($hostel->type=='girls') selected @endif>Girls</option>
                        <option value="mixed" @if($hostel->type=='mixed') selected @endif>Mixed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" class="form-control" value="{{ $hostel->address }}" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control">{{ $hostel->description }}</textarea>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="active" @if($hostel->status=='active') selected @endif>Active</option>
                        <option value="inactive" @if($hostel->status=='inactive') selected @endif>Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update Hostel</button>
                <a href="{{ route('warden.hostels.index') }}" class="btn btn-secondary ml-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection 