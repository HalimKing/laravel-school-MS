<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        // Redirect to dashboard if already logged in
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Email address is required',
            'email.email' => 'Please provide a valid email address',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
        ]);

        // Attempt to authenticate
        if (Auth::attempt($credentials, $request->has('remember'))) {
            // Authentication successful
            $request->session()->regenerate();

            \App\Helpers\SystemLogHelper::log('Login', 'Authentication', 'User logged in successfully: ' . Auth::user()->email);

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Welcome back! You are successfully logged in.');
        }

        \App\Helpers\SystemLogHelper::log('Failed Login', 'Authentication', 'Failed login attempt for email: ' . $request->email);

        // Authentication failed
        throw ValidationException::withMessages([
            'email' => 'These credentials do not match our records.',
        ]);
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        if (Auth::check()) {
            \App\Helpers\SystemLogHelper::log('Logout', 'Authentication', 'User logged out: ' . Auth::user()->email);
        }
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'You have been successfully logged out.');
    }
}
