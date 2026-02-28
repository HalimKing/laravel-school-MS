<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssessmentRequest;
use App\Http\Requests\UpdateAssessmentRequest;
use App\Models\Assessment;
use App\Models\ClassModel;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssessmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $assessments = Assessment::with(['subject', 'class'])->get()->groupBy(function ($item) {
            return $item->subject->id;
        });

        // dd( $assessments->toArray()) ;
        return view('admin.results-managment.assessments.index', compact('assessments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $subjects = Subject::all();
        $classes = ClassModel::all();
        return view('admin.results-managment.assessments.create', compact('subjects', 'classes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssessmentRequest $request)
    {
        // Validate total weight is exactly 100%
        $totalWeight = $request->getTotalWeight();

        if ($totalWeight != 100) {
            return redirect()->back()
                ->withInput()
                ->with('error', "The total assessment weighting must equal exactly 100%. Current total: {$totalWeight}%");
        }

        try {
            DB::beginTransaction();

            // Check if assessments exist for the subject and classes
            $existingAssessments = Assessment::where('subject_id', $request->subject_id)
                ->whereIn('class_id', $request->class_level_ids)
                ->exists();

            if ($existingAssessments) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Assessments already exist for the selected subject and classes. Please delete the existing ones first.");
            }

            // Loop through each selected class level
            foreach ($request->class_level_ids as $classLevelId) {
                // Create each assessment component
                foreach ($request->components as $component) {
                    Assessment::create([
                        'name' => $component['name'],
                        'percentage' => $component['percentage'],
                        'subject_id' => $request->subject_id,
                        'class_id' => $classLevelId,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.results-management.assessments.index')
                ->with('success', 'Assessment structure configured successfully for selected classes.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Assessment Store Error: " . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while saving the assessment structure. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Redirect to index since we manage assessments via index and edit forms
        return redirect()->route('admin.results-management.assessments.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $subject = Subject::findOrFail($id);
        $assessments = Assessment::where('subject_id', $id)->get()->groupBy('class_id');
        $classes = ClassModel::all();

        return view('admin.results-managment.assessments.edit', compact('subject', 'assessments', 'classes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAssessmentRequest $request, string $id)
    {
        // Validate total weight is exactly 100%
        $totalWeight = $request->getTotalWeight();

        if ($totalWeight != 100) {
            return redirect()->back()
                ->withInput()
                ->with('error', "The total assessment weighting must equal exactly 100%. Current total: {$totalWeight}%");
        }

        try {
            DB::beginTransaction();

            // Delete existing assessments for this subject
            Assessment::where('subject_id', $id)->delete();

            // Loop through each selected class level
            foreach ($request->class_level_ids as $classLevelId) {
                // Create each assessment component
                foreach ($request->components as $component) {
                    Assessment::create([
                        'name' => $component['name'],
                        'percentage' => $component['percentage'],
                        'subject_id' => $id,
                        'class_id' => $classLevelId,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.results-management.assessments.index')
                ->with('success', 'Assessment structure updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Assessment Update Error: " . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the assessment structure. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            // Delete all assessments for this subject
            Assessment::where('subject_id', $id)->delete();

            DB::commit();

            return redirect()->route('admin.results-management.assessments.index')
                ->with('success', 'Assessment structure deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Assessment Delete Error: " . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while deleting the assessment structure. Please try again.');
        }
    }
}
