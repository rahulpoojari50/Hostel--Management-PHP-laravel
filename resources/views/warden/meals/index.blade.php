@extends('layouts.admin')

@section('title', 'Meals Management')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <!-- Breadcrumb Navigation -->
        @include('components.breadcrumb-nav', ['breadcrumbs' => $breadcrumbs])
    </div>
    <div>
        <a href="{{ route('warden.meals.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add New Meal
        </a>
    </div>
</div>

<!-- Page Title -->
<div class="mb-4">
    <h5 class="mb-0 text-gray-800">Meals Management</h5>
</div>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-utensils"></i> Meals for Current Week
                        </h5>
                        <a href="{{ route('warden.meals.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Meal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Meals Table -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Meals List</h6>
                </div>
                <div class="card-body">
                    @if($meals->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" id="mealsTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Hostel</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Menu</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($meals as $meal)
                                        <tr>
                                            <td>
                                                <strong>{{ $meal->hostel->name ?? 'N/A' }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $meal->meal_type === 'breakfast' ? 'info' : ($meal->meal_type === 'lunch' ? 'success' : ($meal->meal_type === 'dinner' ? 'primary' : 'warning')) }}">
                                                    {{ ucfirst($meal->meal_type) }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ \Carbon\Carbon::parse($meal->meal_date)->format('d M Y') }}</strong><br>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($meal->meal_date)->format('l') }}</small>
                                            </td>
                                            <td>
                                                @if($meal->menu_description)
                                                    <span class="text-dark">{{ Str::limit($meal->menu_description, 50) }}</span>
                                                @else
                                                    <span class="text-muted">No menu description</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('warden.meals.show', $meal) }}" 
                                                       class="btn btn-info btn-sm" 
                                                       title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('warden.meals.edit', $meal) }}" 
                                                       class="btn btn-warning btn-sm" 
                                                       title="Edit Meal">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('warden.meals.destroy', $meal) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this meal?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-danger btn-sm" 
                                                                title="Delete Meal">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No meals found for this week</h5>
                            <p class="text-muted">Start by adding meals for your hostel students.</p>
                            <a href="{{ route('warden.meals.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add First Meal
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    @if($meals->count() > 0)
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Meals
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $meals->count() }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-utensils fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    This Week
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ now()->startOfWeek()->format('d M') }} - {{ now()->endOfWeek()->format('d M') }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Meal Types
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $meals->pluck('meal_type')->unique()->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-list fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Hostels
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $meals->pluck('hostel_id')->unique()->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-building fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#mealsTable').DataTable({
        "order": [[2, "asc"], [1, "asc"]], // Sort by date, then meal type
        "pageLength": 25,
        "language": {
            "search": "Search meals:",
            "lengthMenu": "Show _MENU_ meals per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ meals"
        }
    });
});
</script>
@endpush
@endsection 