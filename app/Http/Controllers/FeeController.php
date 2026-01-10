<?php

namespace App\Http\Controllers;

use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\Fee;
use App\Models\FeeCategory;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index(Request $request)
{
    // Data for the filter dropdowns
    $feeCategories = FeeCategory::orderBy('name')->get();
    $academicYears = AcademicYear::orderBy('name', 'desc')->get();
    $classes = ClassModel::orderBy('name', 'asc')->get();

    // Check if this is an AJAX request
    if ($request->ajax()) {
        // Start the query with relationships eager loaded
        $query = Fee::with(['feeCategory', 'academicYear', 'academicPeriod', 'class']);

        // Apply Filters based on request input
        if ($request->filled('fee_category_id')) {
            $query->where('fee_category_id', $request->fee_category_id);
        }
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $feeStructures = $query->latest()->get();

        if ($feeStructures->isEmpty()) {
            return response()->json([
                'html' => ''
            ]);
        }

        // Generate HTML for the table body
        $html = '';
        foreach ($feeStructures as $structure) {
            $html .= '<tr>';
            $html .= '<td class="align-middle"><span class="fw-bold">' . ($structure->feeCategory->name ?? 'N/A') . '</span></td>';
            $html .= '<td class="align-middle"><span class="badge bg-light text-secondary border">' . ($structure->academicYear->name ?? 'N/A') . '</span></td>';
            $html .= '<td class="align-middle">' . ($structure->academicPeriod->name ?? 'N/A') . '</td>';
            $html .= '<td class="align-middle">' . ($structure->class->name ?? 'N/A') . '</td>';
            $html .= '<td class="align-middle fw-bold">' . number_format($structure->amount, 2) . '</td>';
            $html .= '<td class="text-end align-middle">';
            $html .= '<div class="dropdown">';
            $html .= '<button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Actions</button>';
            $html .= '<ul class="dropdown-menu dropdown-menu-end shadow-sm">';
            $html .= '<li><a class="dropdown-item d-flex align-items-center" href="' . route('admin.fee-management.fees.edit', $structure->id) . '"><i data-lucide="pencil" class="me-2 text-muted" style="width: 14px;"></i> Edit</a></li>';
            $html .= '<li><hr class="dropdown-divider"></li>';
            $html .= '<li><form class="delete-form" method="POST" action="' . route('admin.fee-management.fees.destroy', $structure->id) . '">';
            $html .= csrf_field();
            $html .= method_field('DELETE');
            $html .= '<button type="submit" class="dropdown-item text-danger d-flex align-items-center"><i data-lucide="trash-2" class="me-2" style="width: 14px;"></i> Delete</button>';
            $html .= '</form></li>';
            $html .= '</ul>';
            $html .= '</div>';
            $html .= '</td>';
            $html .= '</tr>';
        }

        return response()->json([
            'html' => $html
        ]);
    }

    // For initial page load (non-AJAX)
    $query = Fee::with(['feeCategory', 'academicYear', 'academicPeriod', 'class']);

    if ($request->filled('fee_category_id')) {
        $query->where('fee_category_id', $request->fee_category_id);
    }
    if ($request->filled('academic_year_id')) {
        $query->where('academic_year_id', $request->academic_year_id);
    }
    if ($request->filled('class_id')) {
        $query->where('class_id', $request->class_id);
    }

    $feeStructures = $query->latest()->get();

    return view('admin.fee-management.fee.index', compact(
        'feeStructures', 
        'feeCategories', 
        'academicYears', 
        'classes'
    ));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $feeCategories = FeeCategory::orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $academicPeriods = AcademicPeriod::orderBy('name')->get();
        $classes = ClassModel::orderBy('name', 'asc')->get();

        return view('admin.fee-management.fee.create', compact('feeCategories', 'academicYears', 'academicPeriods', 'classes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fee_category_id'    => 'required|exists:fee_categories,id',
            'academic_year_id'   => 'required|exists:academic_years,id',
            'academic_period_id' => 'nullable|exists:academic_periods,id',
            'amount'             => 'required|numeric|min:0',
            'class_id'           => 'nullable|exists:class_models,id',
        ]);

        try {
            // Check for duplicate configuration to prevent double-charging
            $exists = Fee::where([
                'fee_category_id'    => $validated['fee_category_id'],
                'academic_year_id'   => $validated['academic_year_id'],
                'academic_period_id' => $validated['academic_period_id'],
                'class_id'           => $validated['class_id'],
            ])->exists();
            if ($exists) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'A fee structure for this category, year, and period already exists.');
            }

            Fee::create($validated);

            return redirect()->route('admin.fee-management.fees.index')
                ->with('success', 'Fee structure configured successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while saving: ' . $e->getMessage());
        }
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
    public function edit(Fee $fee)
    {
        //
        $feeCategories = FeeCategory::orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $academicPeriods = AcademicPeriod::orderBy('name')->get();
        $classes = ClassModel::orderBy('name', 'asc')->get();

        return view('admin.fee-management.fee.edit', compact('feeCategories', 'academicYears', 'academicPeriods', 'classes', 'fee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fee $fee)
    {
        //
        $validated = $request->validate([
            'fee_category_id'    => 'required|exists:fee_categories,id',
            'academic_year_id'   => 'required|exists:academic_years,id',
            'academic_period_id' => 'nullable|exists:academic_periods,id',
            'amount'             => 'required|numeric|min:0',
            'class_id'           => 'nullable|exists:class_models,id',
        ]);

        try {
            // remove amount from validated
            // $validated
           

            // Check for duplicate configuration to prevent double-charging
            $duplicate = Fee::where([
                'fee_category_id'    => $validated['fee_category_id'],
                'academic_year_id'   => $validated['academic_year_id'],
                'academic_period_id' => $validated['academic_period_id'],
                'class_id'           => $validated['class_id'],
            ])
                ->where('id', '!=', $fee->id)
                ->exists();

            if ($duplicate) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Another fee structure already exists with these exact parameters.');
            }
            $fee->update($validated);

            return redirect()->route('admin.fee-management.fees.index')
                ->with('success', 'Fee structure updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating: ' . $e->getMessage());
        }

    }

    /**
     * Remove the specified resource from storage.
     */ 
    public function destroy(Fee $fee)
    {
        try {
            $fee->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Fee structure deleted successfully.'
                ]);
            }

            return redirect()->route('admin.fee-management.fees.index')
                ->with('success', 'Fee structure deleted successfully.');

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting fee structure.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error deleting fee structure.');
        }
    }
}
