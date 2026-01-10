<?php

namespace App\Http\Controllers;

use App\Models\AcademicPeriod;
use Illuminate\Http\Request;

class AcademicPeriodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $academicPeriods = AcademicPeriod::all();
        return view('admin.academics.academic-period.index', compact('academicPeriods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.academics.academic-period.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => 'required|unique:academic_periods,name',
        ]);

        AcademicPeriod::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.academics.academic-periods.index')->with('success', 'Academic Period created successfully');
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
    public function edit(AcademicPeriod $academicPeriod)
    {
        //

        return view('admin.academics.academic-period.edit', compact('academicPeriod'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcademicPeriod $academicPeriod)
    {
        //

        $request->validate([
            'name' => 'required|unique:academic_periods,name,'.$academicPeriod->id,
        ]);

        $academicPeriod->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.academics.academic-periods.index')->with('success', 'Academic Period updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicPeriod $academicPeriod)
    {
        //
        $academicPeriod->delete();
        return redirect()->route('admin.academics.academic-periods.index')->with('success', 'Academic Period deleted successfully');
    }
}
