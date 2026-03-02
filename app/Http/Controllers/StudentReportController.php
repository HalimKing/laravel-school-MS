<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\LevelData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentReportController extends Controller
{
    /**
     * Generate student report
     */
    public function report(Request $request)
    {
        $query = Student::with(['levels.classModel', 'levels.academicYear']);

        // Apply filters
        if ($request->filled('class_id')) {
            $query->whereHas('levels', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        if ($request->filled('academic_year_id')) {
            $query->whereHas('levels', function ($q) use ($request) {
                $q->where('academic_year_id', $request->academic_year_id);
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'first_name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $students = $query->paginate(20);

        // Get filter options
        $classes = ClassModel::all();
        $academicYears = AcademicYear::all();
        $genders = ['Male', 'Female', 'Other'];

        // Calculate statistics
        $totalStudents = Student::count();
        $maleStudents = Student::where('gender', 'Male')->count();
        $femaleStudents = Student::where('gender', 'Female')->count();
        $activeStudents = Student::where('status', 'active')->count();

        return view('admin.reports.student-report', compact(
            'students',
            'classes',
            'academicYears',
            'genders',
            'totalStudents',
            'maleStudents',
            'femaleStudents',
            'activeStudents'
        ));
    }
}
