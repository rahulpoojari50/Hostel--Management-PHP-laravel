@extends('layouts.admin')

@section('title', 'Edit Meal')

@section('content')
<div class="container-fluid py-4">
    @include('components.breadcrumb', [
        'pageTitle' => 'Edit Meal',
        'breadcrumbs' => [
            ['name' => 'Home', 'url' => url('/')],
            ['name' => 'Meals', 'url' => route('warden.meals.index')],
            ['name' => 'Edit Meal', 'url' => '']
        ]
    ])

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-edit"></i> Edit Meal
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('warden.meals.update', $meal) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="hostel_id" class="font-weight-bold">Hostel <span class="text-danger">*</span></label>
                            <select name="hostel_id" class="form-control @error('hostel_id') is-invalid @enderror" required>
                                <option value="">-- Select Hostel --</option>
                                @foreach($hostels as $hostel)
                                    <option value="{{ $hostel->id }}" 
                                            {{ (old('hostel_id', $meal->hostel_id) == $hostel->id) ? 'selected' : '' }}>
                                        {{ $hostel->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('hostel_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="meal_type" class="font-weight-bold">Meal Type <span class="text-danger">*</span></label>
                            <select name="meal_type" class="form-control @error('meal_type') is-invalid @enderror" required>
                                <option value="">-- Select Meal Type --</option>
                                <option value="breakfast" {{ (old('meal_type', $meal->meal_type) == 'breakfast') ? 'selected' : '' }}>Breakfast</option>
                                <option value="lunch" {{ (old('meal_type', $meal->meal_type) == 'lunch') ? 'selected' : '' }}>Lunch</option>
                                <option value="snacks" {{ (old('meal_type', $meal->meal_type) == 'snacks') ? 'selected' : '' }}>Snacks</option>
                                <option value="dinner" {{ (old('meal_type', $meal->meal_type) == 'dinner') ? 'selected' : '' }}>Dinner</option>
                            </select>
                            @error('meal_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="meal_date" class="font-weight-bold">Meal Date <span class="text-danger">*</span></label>
                            <input type="date" 
                                   name="meal_date" 
                                   class="form-control @error('meal_date') is-invalid @enderror" 
                                   value="{{ old('meal_date', $meal->meal_date) }}"
                                   required>
                            @error('meal_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="menu_description" class="font-weight-bold">Menu Description</label>
                            <textarea name="menu_description" 
                                      class="form-control @error('menu_description') is-invalid @enderror" 
                                      rows="4" 
                                      placeholder="Describe the menu items for this meal...">{{ old('menu_description', $meal->menu_description) }}</textarea>
                            @error('menu_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Meal
                            </button>
                            <a href="{{ route('warden.meals.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 