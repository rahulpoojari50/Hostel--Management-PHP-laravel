@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Meal Details</h1>
    <div class="mb-4">
        <strong>Hostel:</strong> {{ $meal->hostel->name ?? '-' }}<br>
        <strong>Type:</strong> {{ ucfirst($meal->meal_type) }}<br>
        <strong>Date:</strong> {{ $meal->meal_date }}<br>
        <strong>Menu:</strong> {{ $meal->menu_description }}<br>
    </div>
    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4">Attendance</h2>
        <form action="{{ route('warden.meals.update', $meal) }}" method="POST">
            @csrf
            @method('PUT')
            <table class="min-w-full bg-white shadow rounded">
                <thead>
                    <tr>
                        <th class="px-4 py-2">Student</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                        <tr>
                            <td class="border px-4 py-2">{{ $student->name }}</td>
                            <td class="border px-4 py-2 capitalize">{{ $attendance[$student->id] ?? 'absent' }}</td>
                            <td class="border px-4 py-2">
                                <button name="attendance[{{ $student->id }}]" value="present" class="text-green-600">Mark Present</button>
                                <button name="attendance[{{ $student->id }}]" value="absent" class="text-red-600">Mark Absent</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
    </div>
    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4">Attendance Report</h2>
        <div>Present: {{ $presentCount }}</div>
        <div>Absent: {{ $absentCount }}</div>
        <div>Total: {{ $totalCount }}</div>
        <div>Attendance %: {{ $attendancePercent }}%</div>
    </div>
    <a href="{{ route('warden.meals.index') }}" class="text-blue-600">Back to Meals</a>
</div>
@endsection 