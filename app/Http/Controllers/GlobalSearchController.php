<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\StudentFee;

class GlobalSearchController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $students = collect();
        $hostels = collect();
        $rooms = collect();
        $fees = collect();
        if ($q) {
            $students = User::where('role', 'student')
                ->where(function($query) use ($q) {
                    $query->where('name', 'like', "%$q%")
                        ->orWhere('email', 'like', "%$q%")
                        ->orWhere('parent_email', 'like', "%$q%")
                        ->orWhere('phone', 'like', "%$q%")
                        ;
                })
                ->with(['roomAssignments.room.hostel'])
                ->limit(10)->get();
            $hostels = Hostel::where('name', 'like', "%$q%")
                ->orWhere('type', 'like', "%$q%")
                ->orWhere('address', 'like', "%$q%")
                ->limit(10)->get();
            $rooms = Room::where('room_number', 'like', "%$q%")
                ->orWhereHas('hostel', function($query) use ($q) {
                    $query->where('name', 'like', "%$q%")
                        ->orWhere('address', 'like', "%$q%")
                        ;
                })
                ->with('hostel')
                ->limit(10)->get();
            $fees = StudentFee::where('fee_type', 'like', "%$q%")
                ->orWhere('amount', 'like', "%$q%")
                ->with('student')
                ->limit(10)->get();
        }
        return view('search', compact('q', 'students', 'hostels', 'rooms', 'fees'));
    }
} 