<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\LevelData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollStudentstController extends Controller
{
    //
    public function index()
    {
        $academicYears = AcademicYear::all();
        $classes = ClassModel::all();
        
        return view('admin.enrollments.enroll-student.index', compact('academicYears', 'classes'));
    }


    public function enrollFilter(Request $request)
    {
        $request->validate([
            'filter_year' => 'required|exists:academic_years,id',
            'filter_class' => 'required|exists:class_models,id',
        ]);
        // dd($request->all());

        $academicYears = AcademicYear::all();
        $classes = ClassModel::all();
        
        // Fetch students matching the current filter criteria
        $students = LevelData::with('student')
                           ->where('academic_year_id', $request->filter_year)
                           ->where('class_id', $request->filter_class)
                           ->get();

        // dd($students);
        return view('admin.enrollments.enroll-student.index', compact('students', 'academicYears', 'classes'));
    }

    public function enrollProcess(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'target_year' => 'required|exists:academic_years,id',
            'target_class' => 'required|exists:class_models,id',
        ]);

        DB::beginTransaction();

        try {
            // Update the selected students to the new Academic Year and Class
            // Create new level data, avoiding duplicate enrollments
            $enrolledCount = 0;
            
            foreach ($request->student_ids as $studentId) {
                $exists = LevelData::where('student_id', $studentId)
                                  ->where('academic_year_id', $request->target_year)
                                  ->exists();
                
                if (!$exists) {
                    LevelData::create([
                        'student_id' => $studentId,
                        'academic_year_id' => $request->target_year,
                        'class_id' => $request->target_class,
                    ]);
                    $enrolledCount++;
                }
            }

            DB::commit();

            $duplicateCount = count($request->student_ids) - $enrolledCount;
            $message = $enrolledCount . ' students have been successfully enrolled into the new period.';
            if ($duplicateCount > 0) {
                $message .= ' (' . $duplicateCount . ' students were already enrolled)';
            }

            return redirect()->route('admin.enrollments.enroll-students.index')
                             ->with('success', $message);
                             
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->with('error', 'An error occurred during enrollment: ' . $e->getMessage());
        }
    }
}
