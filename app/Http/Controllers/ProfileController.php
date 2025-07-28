<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $profile = $user->studentProfile;
        return view('profile.edit', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $request->validate([
                'photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
            $path = $request->file('photo')->store('photos', 'public');
            $data['photo_path'] = $path; // Store photo in photo_path field
        }
        
        // Handle document upload
        if ($request->hasFile('document')) {
            $path = $request->file('document')->store('documents', 'public');
            $data['document_path'] = $path;
        }

        $profile = $user->studentProfile ?: $user->studentProfile()->make();
        $profile->fill($data);
        $profile->user_id = $user->id;
        $profile->save();

        // Force reload of the relationship for the next request
        $user->unsetRelation('studentProfile');

        return Redirect::route('student.profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
