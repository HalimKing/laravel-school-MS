<?php

namespace App\Http\Controllers;

use App\Models\FeeCategory;
use Illuminate\Http\Request;

class FeeCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $feeCategories = \App\Models\FeeCategory::all();
        return view('admin.fee-management.fee-category.index', compact('feeCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.fee-management.fee-category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'name' => 'required|unique:fee_categories,name',
            'description' => 'required',
        ]);

       FeeCategory::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return redirect()->route('admin.fee-management.fee-categories.index')->with('success', 'Fee Category created successfully');
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
    public function edit(FeeCategory $feeCategory)
    {
        //
        return view('admin.fee-management.fee-category.edit', compact('feeCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FeeCategory $feeCategory)
    {
        //
        $validated = $request->validate([
            'name' => 'required|unique:fee_categories,name,' . $feeCategory->id,
            'description' => 'required',
        ]);

        $feeCategory->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return redirect()->route('admin.fee-management.fee-categories.index')->with('success', 'Fee Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FeeCategory $feeCategory)
    {
        //
        $feeCategory->delete();
        return redirect()->route('admin.fee-management.fee-categories.index')->with('success', 'Fee Category deleted successfully');
    }
}
