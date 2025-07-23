@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Edit Meal</h1>
    <form action="{{ route('warden.meals.update', $meal) }}" method="POST" class="bg-white shadow rounded p-6">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Hostel</label>
            <select name="hostel_id" class="w-full border rounded px-3 py-2" required>
                @foreach($hostels as $hostel)
                    <option value="{{ $hostel->id }}" @if($meal->hostel_id == $hostel->id) selected @endif>{{ $hostel->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Meal Type</label>
            <select name="meal_type" class="w-full border rounded px-3 py-2" required>
                <option value="breakfast" @if($meal->meal_type=='breakfast') selected @endif>Breakfast</option>
                <option value="lunch" @if($meal->meal_type=='lunch') selected @endif>Lunch</option>
                <option value="snacks" @if($meal->meal_type=='snacks') selected @endif>Snacks</option>
                <option value="dinner" @if($meal->meal_type=='dinner') selected @endif>Dinner</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Meal Date</label>
            <input type="date" name="meal_date" class="w-full border rounded px-3 py-2" value="{{ $meal->meal_date }}" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Menu Description</label>
            <textarea name="menu_description" class="w-full border rounded px-3 py-2">{{ $meal->menu_description }}</textarea>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update Meal</button>
        <a href="{{ route('warden.meals.index') }}" class="ml-4 text-gray-600">Cancel</a>
    </form>
</div>
@endsection 