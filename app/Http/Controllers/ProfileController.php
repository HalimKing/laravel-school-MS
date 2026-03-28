<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show the profile page
     */
    public function show()
    {
        $user = Auth::user();
        $roles = $user->getRoleNames();

        return view('profile.show', compact('user', 'roles'));
    }

    /**
     * Update user profile information
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Update user password
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = Auth::user();

        // Verify the current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update the password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Password updated successfully!');
    }
}
