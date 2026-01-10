<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $sessions = \App\Models\AcademicYear::all();
        return view('admin.academic-year.index', compact('sessions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.academic-year.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => 'required|unique:academic_years,name',
        ]);

        \App\Models\AcademicYear::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.sessions.index')->with('success', 'Session created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicYear $session)
    {
        //
        return view('admin.academic-year.edit', compact('session'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcademicYear $session)
    {
        //
        $request->validate([
            'name' => 'required|unique:academic_years,name,'.$session->id,
        ]);

        $session->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.sessions.index')->with('success', 'Session updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicYear $session)
    {
        //
        $session->delete();
        return redirect()->route('admin.sessions.index')->with('success', 'Session deleted successfully');
    }

    public function activateSessions(Request $request, String $id)
    {
        \App\Models\AcademicYear::where('id', '!=', $id)->update(['status' => 'inactive']);
        \App\Models\AcademicYear::where('id', $id)->update(['status' => 'active']);
        return redirect()->route('admin.sessions.index')->with('success', 'Session updated successfully');
    }
}
