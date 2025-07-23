@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Meals</h1>
    <a href="{{ route('warden.meals.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded mb-4 inline-block">Add Meal</a>
    <table class="min-w-full bg-white shadow rounded">
        <thead>
            <tr>
                <th class="px-4 py-2">Hostel</th>
                <th class="px-4 py-2">Type</th>
                <th class="px-4 py-2">Date</th>
                <th class="px-4 py-2">Menu</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($meals as $meal)
                <tr>
                    <td class="border px-4 py-2">{{ $meal->hostel->name ?? '-' }}</td>
                    <td class="border px-4 py-2 capitalize">{{ $meal->meal_type }}</td>
                    <td class="border px-4 py-2">{{ $meal->meal_date }}</td>
                    <td class="border px-4 py-2">{{ $meal->menu_description }}</td>
                    <td class="border px-4 py-2">
                        <a href="{{ route('warden.meals.show', $meal) }}" class="text-blue-600">Details</a> |
                        <a href="{{ route('warden.meals.edit', $meal) }}" class="text-yellow-600">Edit</a> |
                        <form action="{{ route('warden.meals.destroy', $meal) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600" onclick="return confirm('Delete this meal?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-4">No meals found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection 