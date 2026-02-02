<?php

namespace App\Http\Controllers;

use App\Http\Requests\Storesubjectassignmentrequest;
use App\Models\AcademicYear;
use App\Models\AssignSubject;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use PhpParser\Node\Expr\Assign;

class AssignSujectsController extends Controller
{
    //

   public function index(Request $request)
    {
        $query = AssignSubject::with(['teacher', 'subject', 'class', 'academicYear']);

        // Filter by class
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by subject
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        // Search by teacher name
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('teacher', function($q) use ($searchTerm) {
                $q->where('first_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('last_name', 'LIKE', "%{$searchTerm}%");
            });
        }

        $assignments = $query->latest()->paginate(15);

        // Get data for filters and form
        $teachers = Teacher::where('status', 'active')->orderBy('first_name')->get();
        $subjects = Subject::orderBy('name')->get();
        $classes = ClassModel::orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        // Statistics
        $totalSubjects = Subject::count();
        $activeAssignments = AssignSubject::count();
        $totalTeachers = Teacher::where('status', 'active')->count();
        $totalClasses = ClassModel::count();

        return view('admin.academics.assign-subjects.index', compact(
            'assignments',
            'teachers',
            'subjects',
            'classes',
            'academicYears',
            'totalSubjects',
            'activeAssignments',
            'totalTeachers',
            'totalClasses'
        ));
    }

    // create

    public function create()
    {
        $teachers = Teacher::orderBy('first_name')->get();
        $subjects = Subject::orderBy('name')->get();
        $classes = ClassModel::orderBy('name')->get();

        return view('admin.academics.assign-subjects.create', compact('teachers', 'subjects', 'classes'));
    }


   public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:class_models,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        // Check for duplicate assignment
        $exists = AssignSubject::where('teacher_id', $validated['teacher_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('class_id', $validated['class_id'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This assignment already exists for the selected teacher, subject, class, and academic year.');
        }

        AssignSubject::create($validated);

        return redirect()->route('admin.academics.assign-subjects.index')
            ->with('success', 'Subject assignment created successfully.');
    }


public function edit(String $id)
    {
        $subjectAssignment = AssignSubject::findOrFail($id);
        // dd($subjectAssignment);
        $teachers = Teacher::where('status', 'active')->orderBy('first_name')->get();
        $subjects = Subject::orderBy('name')->get();
        $classes = ClassModel::orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        return view('admin.academics.assign-subjects.edit', compact(
            'subjectAssignment',
            'teachers',
            'subjects',
            'classes',
            'academicYears'
        ));
    }

    public function update(Request $request, String $id)
    {
        $subjectAssignment = AssignSubject::findOrFail($id);
        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:class_models,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'status' => 'required|in:active,inactive',
        ]);

        try {
            // Check for duplicate assignment (excluding current record)
            $exists = AssignSubject::where('teacher_id', $validated['teacher_id'])
                ->where('subject_id', $validated['subject_id'])
                ->where('class_id', $validated['class_id'])
                ->where('academic_year_id', $validated['academic_year_id'])
                ->where('id', '!=', $subjectAssignment->id)
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'This assignment already exists for the selected teacher, subject, class, and academic year.');
            }

            $subjectAssignment->update($validated);

            return redirect()->route('admin.academics.assign-subjects.index')
            ->with('success', 'Subject assignment updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the subject assignment.');
        }
    }


    public function destroy(String $id)
    {
        $subjectAssignment = AssignSubject::findOrFail($id);
        $subjectAssignment->delete();

        return redirect()->route('admin.academics.assign-subjects.index')
            ->with('success', 'Subject assignment deleted successfully.');
    }
}
