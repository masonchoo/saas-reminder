<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Handles the form submission
    public function store(Request $request)
    {
        // 1. Validate the request
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Attempt authentication
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // 3. Prevent session fixation attacks
            $request->session()->regenerate();

            // 4. Redirect to the intended page or dashboard
            return redirect()->intended('/dashboard');
        }

        // 5. If it fails, return back with a specific error
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    // Handles logout
    public function destroy(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}
