<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class TeacherPasswordController extends Controller
{
    /**
     * Display the password reset search page.
     */
    public function index()
    {
        return view('admin.teacher.password-management');
    }

    /**
     * Search for a teacher by Staff ID or Email.
     */
    public function search(Request $request)
    {
        $request->validate([
            'search_query' => 'required|string',
        ]);

        $query = $request->input('search_query');

        // Search by email or staff_id
        $teacher = Teacher::where('email', $query)
            ->orWhere('staff_id', $query)
            ->first();

        return view('admin.teacher.password-management', compact('teacher'));
    }

    /**
     * Update the teacher's password in the database.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        try {
            $teacher = Teacher::findOrFail($id);
            
            $teacher->update([
                'password' => Hash::make($request->password),
            ]);

            return redirect()
                ->route('admin.teacher.password.index')
                ->with('success', "Password for {$teacher->first_name} has been reset successfully.");
                
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while resetting the password. Please try again.');
        }
    }
}   