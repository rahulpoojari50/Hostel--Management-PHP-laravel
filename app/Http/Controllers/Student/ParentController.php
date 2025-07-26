<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentController extends Controller
{
    public function edit()
    {
        $student = Auth::user();
        return view('student.parents.edit', compact('student'));
    }

    public function update(Request $request)
    {
        $student = Auth::user();
        $validated = $request->validate([
            'parent_mobile' => 'required|string|max:20',
            'parent_email' => 'required|email|max:255',
            'alternate_mobile' => 'nullable|string|max:20',
        ]);
        // Save to user profile
        $student->parent_mobile = $validated['parent_mobile'];
        $student->parent_email = $validated['parent_email'];
        $student->alternate_mobile = $validated['alternate_mobile'];
        $student->save();
        return redirect()->route('student.parents.edit')->with('success', 'Parent details updated!');
    }
} 