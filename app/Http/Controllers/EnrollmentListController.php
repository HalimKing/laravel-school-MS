<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\LevelData;
use App\Models\Student;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // Ensure this matches exactly

class EnrollmentListController extends Controller
{
    //
    public function index(Request $request)
    {
        $academicYears = AcademicYear::all();
        $classes = ClassModel::all();

        // Initialize students variable
        $students = null;

        // Only query if filters are applied
         if ($request->filled('academic_year') && $request->filled('class')) {
        $students = LevelData::with(['student', 'class', 'academicYear'])
            ->where('academic_year_id', $request->academic_year)
            ->where('class_id', $request->class)
            ->orderBy(
                Student::select('first_name')
                    ->whereColumn('students.id', 'level_data.student_id')
                    ->limit(1)
            )
            ->paginate(50);
    }

        return view('admin.enrollments.enrollment-list.index', compact('academicYears', 'classes', 'students'));
    }


    /**
     * Export filtered enrollment data to CSV or PDF.
     */
    public function export(Request $request)
    {
        $request->validate([
            'type' => 'required|in:csv,pdf',
            'year' => 'required|exists:academic_years,id',
            'class' => 'required|exists:class_models,id',
        ]);

        $year = AcademicYear::findOrFail($request->year);
        $class = ClassModel::findOrFail($request->class);

        $students = LevelData::where('academic_year_id', $request->year)
            ->where('class_id', $request->class)
            ->get();

        if ($request->type === 'csv') {
            return $this->exportCsv($students, $year, $class);
        }

         if ($request->type === 'pdf') {
            return $this->exportPdf($students, $year, $class);
        }

        // Logic for PDF export would go here (e.g., using Snappy or DomPDF)
        return back()->with('error', 'PDF export functionality is being configured.');
    }


     private function exportCsv($students, $year, $class)
    {
        $fileName = "Enrollment_List_{$year->name}_{$class->name}.csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Student ID', 'First Name', 'Last Name', 'Other Name', 'Gender', 'Academic Year', 'Class'];

        $callback = function() use($students, $columns, $year, $class) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($students as $student) {
                fputcsv($file, [
                    $student->student->student_id,
                    ucfirst($student->student->first_name),
                    ucfirst($student->student->last_name),
                    ucfirst($student->student->other_name),
                    ucfirst($student->student->gender),
                    $year->name,
                    $class->name,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportPdf($students, $year, $class)
    {
        $data = [
            'title' => 'Student Enrollment List',
            'date' => date('m/d/Y'),
            'year' => $year->name,
            'class' => $class->name,
            'students' => $students
        ];

        $pdf = PDF::loadView('exports.enrollment_list_pdf', $data);
        
        // Optional: Set paper size and orientation
        $pdf->setPaper('a4', 'portrait');

         // Sanitize the filename
        $yearName = str_replace(['/', '\\'], '-', $year->name);
        $className = str_replace(['/', '\\'], '-', $class->name);
        
        $fileName = "Enrollment_List_{$yearName}_{$className}.pdf";
        
        return $pdf->download($fileName);
    }

   
}
