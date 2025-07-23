@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Add Meal</h1>
    <form action="{{ route('warden.meals.store') }}" method="POST" class="bg-white shadow rounded p-6">
        @csrf
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Hostel</label>
            <select name="hostel_id" class="w-full border rounded px-3 py-2" required>
                @foreach($hostels as $hostel)
                    <option value="{{ $hostel->id }}">{{ $hostel->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Meal Type</label>
            <select name="meal_type" class="w-full border rounded px-3 py-2" required>
                <option value="breakfast">Breakfast</option>
                <option value="lunch">Lunch</option>
                <option value="snacks">Snacks</option>
                <option value="dinner">Dinner</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Meal Date</label>
            <input type="date" name="meal_date" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Menu Description</label>
            <textarea name="menu_description" class="w-full border rounded px-3 py-2"></textarea>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Create Meal</button>
        <a href="{{ route('warden.meals.index') }}" class="ml-4 text-gray-600">Cancel</a>
    </form>
</div>
@endsection 