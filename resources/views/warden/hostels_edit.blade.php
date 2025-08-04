@extends('layouts.admin')

@section('title', 'Edit Hostel')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <!-- Breadcrumb Navigation -->
        @include('components.breadcrumb-nav', ['breadcrumbs' => $breadcrumbs])
    </div>
    <div>
        {{-- Action buttons can go here --}}
    </div>
</div>

<!-- Page Title -->
<div class="mb-4">
    <h5 class="mb-0 text-gray-800">Edit Hostel</h5>
</div>

<div class="container-fluid py-4">
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
                <a href="{{ route('warden.dashboard') }}" class="btn btn-secondary ml-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection 