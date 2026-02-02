<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $subjects = \App\Models\Subject::all();
        return view('admin.academics.subject.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.academics.subject.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => 'required|unique:subjects,name',
            'code' => 'required|unique:subjects,code',
            'type' => 'required|in:core,elective',
        ]);

        $subject = \App\Models\Subject::create($request->all());
        return redirect()->route('admin.academics.subjects.index')->with('success', 'Subject created successfully.');
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
    public function edit(Subject $subject)
    {
        //
        return view('admin.academics.subject.edit', compact('subject'));
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        //
        $request->validate([
            'name' => 'required|unique:subjects,name,' . $subject->id,
            'code' => 'required|unique:subjects,code,' . $subject->id,
            'type' => 'required|in:core,elective',
        ]);

        $subject->update($request->all());
        return redirect()->route('admin.academics.subjects.index')->with('success', 'Subject updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        //
        $subject->delete();
        return redirect()->route('admin.academics.subjects.index')->with('success', 'Subject deleted successfully.');
    }
}
