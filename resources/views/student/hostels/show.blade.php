@extends('layouts.admin')

@section('title', 'Hostel Details')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $hostel->name }}</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2"><strong>Location:</strong> {{ $hostel->location ?? $hostel->address }}</div>
                    <div class="mb-2"><strong>Description:</strong> {{ $hostel->description }}</div>
                    <div class="mb-2"><strong>Weekly Meals Menu:</strong></div>
                    @php
                        $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
                        $mealTypes = ['breakfast','lunch','snacks','dinner'];
                        $menu = $hostel->menu ?? [];
                        $hasMenuData = !empty(array_filter($menu, function($day) { return !empty(array_filter($day)); }));
                    @endphp
                    
                    @if($hasMenuData)
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered mb-0">
                                <thead class="thead-light">
                                    <tr><th>Day</th><th>Breakfast</th><th>Lunch</th><th>Snacks</th><th>Dinner</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($days as $day)
                                        <tr>
                                            <td class="font-weight-bold">{{ $day }}</td>
                                            @foreach($mealTypes as $meal)
                                                <td>{{ $menu[$day][$meal] ?? '-' }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle"></i> No weekly menu has been set by the warden yet.
                        </div>
                    @endif
                    <div class="mb-2"><strong>Room Types:</strong></div>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered mb-0">
                            <thead class="thead-light">
                                <tr><th>Type</th><th>Rooms</th><th>Available</th><th>Occupied</th><th>Rent</th></tr>
                            </thead>
                            <tbody>
                                @foreach($hostel->roomTypes as $type)
                                    @php
                                        $totalRooms = $type->rooms->count();
                                        $available = $type->rooms->where('status', 'available')->count();
                                        $occupied = $totalRooms - $available;
                                        $baseRent = $type->price_per_month;
                                        $fees = method_exists($hostel, 'getTotalFeesForRoomType') ? $hostel->getTotalFeesForRoomType($type->id) : 0;
                                        $total = $baseRent + $fees;
                                    @endphp
                                    <tr>
                                        <td>{{ $type->type }}</td>
                                        <td>{{ $totalRooms }}</td>
                                        <td>{{ $available }}</td>
                                        <td>{{ $occupied }}</td>
                                        <td>₹{{ number_format($total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <a href="{{ route('student.applications.create', $hostel->id) }}" class="btn btn-success mt-2">
                        <i class="fas fa-file-alt"></i> Apply
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 