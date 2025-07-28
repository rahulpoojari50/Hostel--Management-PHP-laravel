<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required'],
            'password' => ['required'],
            'role' => ['required', 'in:student,warden'],
        ]);

        $loginField = $credentials['email'];
        $password = $credentials['password'];
        $role = $credentials['role'];

        // Find user by email or USN
        $user = null;
        if (filter_var($loginField, FILTER_VALIDATE_EMAIL)) {
            // It's an email
            $user = \App\Models\User::where('email', $loginField)->where('role', $role)->first();
        } else {
            // It's a USN
            $user = \App\Models\User::where('usn', $loginField)->where('role', $role)->first();
        }

        // Check if user exists and password is correct
        if ($user && \Illuminate\Support\Facades\Hash::check($password, $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();

            if ($role === 'warden') {
                return redirect('/warden/dashboard');
            } else {
                return redirect('/student/dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
