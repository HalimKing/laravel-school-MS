<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $classes = \App\Models\ClassModel::all();
        return view('admin.class.index', compact('classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.class.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = $request->validate( [
            'name' => 'required|unique:class_models,name',
            'description' => 'required',
        ]);

        

        ClassModel::create($validator);

        return redirect()->route('admin.class.index')->with('success', 'Class created successfully');
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
    public function edit(ClassModel $class)
    {
        //

        return view('admin.class.edit', compact('class'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClassModel $class)
    {
        //

        $validator = $request->validate( [
            'name' => 'required|unique:class_models,name,'.$class->id,
            'description' => 'required',
        ]);

        $class->update($validator);

        return redirect()->route('admin.class.index')->with('success', 'Class updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassModel $class)
    {
        //
        $class->delete();
        return redirect()->route('admin.class.index')->with('success', 'Class deleted successfully');
    }
}
