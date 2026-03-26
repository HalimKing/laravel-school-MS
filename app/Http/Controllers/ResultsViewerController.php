<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\AcademicPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultsViewerController extends Controller
{
    /**
     * Display results based on user role
     * Parents see their children's results
     * Admins see all students' results
     */
    public function viewResults(Request $request)
    {
        $user = Auth::user();
        $resultsData = [];
        $filters = [
            'academic_year_id' => $request->academic_year_id,
            'academic_period_id' => $request->academic_period_id,
            'student_id' => $request->student_id,
        ];

        // Determine which students the user can view
        if ($user->hasRole('parent')) {
            // Parent can only see their own children's results
            $students = Student::where('parent_email', $user->email)->get();
            $pageTitle = "My Children's Results";
        } elseif ($user->hasRole(['admin', 'super-admin']) || $user->can('academic.read')) {
            // Admin can see all students' results
            $students = Student::all();
            $pageTitle = "All Students' Results";
        } elseif ($user->hasRole('student')) {
            // Student can only see their own results
            $student = Student::where('student_id', $user->id)->first();
            if (!$student) {
                return redirect()->back()->with('error', 'Student record not found.');
            }
            $students = collect([$student]);
            $pageTitle = "My Results";
        } else {
            return redirect()->back()->with('error', 'You do not have permission to view results.');
        }

        // If no students, show empty state
        if ($students->isEmpty()) {
            return view('results.view', [
                'studentResults' => [],
                'students' => [],
                'pageTitle' => $pageTitle,
                'academicYears' => AcademicYear::all(),
                'academicPeriods' => AcademicPeriod::all(),
                'filters' => $filters,
                'empty' => true,
            ]);
        }

        // Get results for the students
        $resultsQuery = Result::with(['student', 'assessment.subject', 'academicYear', 'academicPeriod'])
            ->whereIn('student_id', $students->pluck('id'));

        // Apply filters if provided
        if ($filters['academic_year_id']) {
            $resultsQuery->where('academic_year_id', $filters['academic_year_id']);
        }
        if ($filters['academic_period_id']) {
            $resultsQuery->where('academic_period_id', $filters['academic_period_id']);
        }
        if ($filters['student_id']) {
            $resultsQuery->where('student_id', $filters['student_id']);
        }

        $results = $resultsQuery->get();

        // Organize results by student
        $studentResults = [];
        $assessmentNames = [];
        $subjectsList = [];

        foreach ($results as $result) {
            $studentId = $result->student->id;
            $studentName = $result->student->first_name . ' ' . $result->student->last_name;
            $assessmentName = $result->assessment->name;
            $subjectName = $result->assessment->subject->name ?? 'Unknown Subject';

            if (!isset($studentResults[$studentId])) {
                $studentResults[$studentId] = [
                    'student_id' => $studentId,
                    'student_name' => $studentName,
                    'student_record' => $result->student,
                    'subjects' => [],
                    'assessments' => [],
                    'scores' => [],
                    'resultIds' => [],
                    'academicYear' => $result->academicYear,
                    'academicPeriod' => $result->academicPeriod,
                ];
            }

            // Track subjects
            if (!isset($subjectsList[$subjectName])) {
                $subjectsList[$subjectName] = $result->assessment->subject;
            }

            // Track assessments per subject/student
            $key = $subjectName . '|' . $assessmentName;
            $studentResults[$studentId]['assessments'][$key] = [
                'name' => $assessmentName,
                'subject' => $subjectName,
            ];
            $studentResults[$studentId]['scores'][$key] = $result->score;
            $studentResults[$studentId]['resultIds'][$key] = $result->id;

            if (!in_array($assessmentName, $assessmentNames)) {
                $assessmentNames[] = $assessmentName;
            }

            // Group by subject
            if (!isset($studentResults[$studentId]['subjects'][$subjectName])) {
                $studentResults[$studentId]['subjects'][$subjectName] = [
                    'assessments' => [],
                    'total_score' => 0,
                    'assessment_count' => 0,
                ];
            }

            $studentResults[$studentId]['subjects'][$subjectName]['assessments'][$assessmentName] = $result->score;
            $studentResults[$studentId]['subjects'][$subjectName]['total_score'] += $result->score;
            $studentResults[$studentId]['subjects'][$subjectName]['assessment_count']++;
        }

        // Calculate subject averages
        foreach ($studentResults as &$data) {
            foreach ($data['subjects'] as &$subject) {
                $subject['average'] = $subject['assessment_count'] > 0
                    ? $subject['total_score'] / $subject['assessment_count']
                    : 0;
            }
        }

        return view('results.view', [
            'studentResults' => $studentResults,
            'students' => $students,
            'pageTitle' => $pageTitle,
            'academicYears' => AcademicYear::all(),
            'academicPeriods' => AcademicPeriod::all(),
            'subjectsList' => $subjectsList,
            'assessmentNames' => $assessmentNames,
            'filters' => $filters,
            'userRole' => $user->roles->first()->name ?? 'user',
        ]);
    }

    /**
     * Get students for filter dropdown (AJAX)
     */
    public function getStudents(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('parent')) {
            $students = Student::where('parent_email', $user->email)->get();
        } else {
            $students = Student::all();
        }

        return response()->json(
            $students->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->first_name . ' ' . $s->last_name,
            ])
        );
    }

    /**
     * Export results as PDF or Excel (optional)
     */
    public function exportResults(Request $request)
    {
        $user = Auth::user();
        $format = $request->format ?? 'pdf'; // pdf or excel

        // Similar filtering logic as viewResults
        if ($user->hasRole('parent')) {
            $students = Student::where('parent_email', $user->email)->get();
        } elseif ($user->hasRole(['admin', 'super-admin'])) {
            $students = Student::all();
        } else {
            return redirect()->back()->with('error', 'You do not have permission to export results.');
        }

        // Get results and prepare for export
        $results = Result::with(['student', 'assessment.subject', 'academicYear', 'academicPeriod'])
            ->whereIn('student_id', $students->pluck('id'))
            ->get();

        // For now, just return success message
        // You can integrate dompdf or maatwebsite/excel for actual export
        return redirect()->back()->with('success', 'Results exported successfully!');
    }
}
