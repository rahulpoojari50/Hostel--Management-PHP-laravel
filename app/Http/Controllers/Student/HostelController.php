<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hostel;
use App\Models\RoomType;

class HostelController extends Controller
{
    public function index()
    {
        $hostels = Hostel::with('roomTypes')->get();
        return view('student.hostels.index', compact('hostels'));
    }

    public function show($id)
    {
        $hostel = Hostel::with(['roomTypes.rooms', 'meals'])->findOrFail($id);
        return view('student.hostels.show', compact('hostel'));
    }
}
