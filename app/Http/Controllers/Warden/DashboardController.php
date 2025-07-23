<?php

namespace App\Http\Controllers\Warden;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Hostel;
use App\Models\RoomApplication;
use App\Models\Room;

class DashboardController extends Controller
{
    public function index()
    {
        $warden = auth()->user();
        $hostels = Hostel::where('warden_id', $warden->id)->get();
        $hostelIds = $hostels->pluck('id');

        // Statistics
        $totalStudents = RoomApplication::whereIn('hostel_id', $hostelIds)->where('status', 'approved')->count();
        $totalHostels = $hostels->count();
        $totalRooms = Room::whereIn('hostel_id', $hostelIds)->count();
        $pendingApplications = RoomApplication::whereIn('hostel_id', $hostelIds)->where('status', 'pending')->count();

        // Recent applications
        $recentApplications = RoomApplication::whereIn('hostel_id', $hostelIds)
            ->latest()->take(5)->with(['student', 'roomType'])->get();

        // Occupancy rates
        $occupancy = [];
        foreach ($hostels as $hostel) {
            $totalRoomsInHostel = $hostel->rooms()->count();
            $occupiedRooms = $hostel->rooms()->where('status', 'occupied')->count();
            $occupancy[$hostel->name] = $totalRoomsInHostel > 0 ? round(($occupiedRooms / $totalRoomsInHostel) * 100, 2) : 0;
        }

        $pageTitle = 'Hostel Dashboard';
        $breadcrumbs = [
            ['name' => 'Home', 'url' => url('/')],
            ['name' => 'Hostel Dashboard', 'url' => '']
        ];
        return view('warden.dashboard', compact(
            'totalStudents',
            'totalHostels',
            'totalRooms',
            'pendingApplications',
            'recentApplications',
            'occupancy',
            'hostels',
            'pageTitle',
            'breadcrumbs'
        ));
    }
}
