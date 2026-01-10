<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $teachers = \App\Models\Teacher::all();
        return view('admin.teacher.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.teacher.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'staff_id' => 'required|unique:teachers,staff_id',
            'email' => 'required|email|unique:teachers,email',
            'phone' => 'required',
            'status' => 'required',
            'address' => 'nullable|string',
        ]);
        $password = Hash::make('password');
        Teacher::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'staff_id' => $request->staff_id,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
            'password' => $password,
            'address' => $request->address,
        ]);
        return redirect()->route('admin.teachers.index')->with('success', 'Teacher created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher)
    {
        //
        return view('admin.teacher.show', compact('teacher'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher)
    {
        //
        return view('admin.teacher.edit', compact('teacher'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teacher $teacher)
    {
        //
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'staff_id' => 'required|unique:teachers,staff_id,'.$teacher->id,
            'email' => 'required|email|unique:teachers,email,'.$teacher->id,
            'phone' => 'required',
            'status' => 'required',
            'address' => 'nullable|string',
        ]);
        $teacher->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'staff_id' => $request->staff_id,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
            'address' => $request->address,
        ]);
        return redirect()->route('admin.teachers.index')->with('success', 'Teacher updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        //
        $teacher->delete();
        return redirect()->route('admin.teachers.index')->with('success', 'Teacher deleted successfully');
    }


        /**
     * Update the teacher's password in the database.
     */
    public function resetPassword(Request $request, $id)
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
}
