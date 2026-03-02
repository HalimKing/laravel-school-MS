<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\LevelData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceReportController extends Controller
{
    /**
     * Generate attendance report
     */
    public function attendanceReport(Request $request)
    {
        $query = Attendance::with(['student', 'classModel', 'academicYear']);

        // Apply filters
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->where('attendance_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('attendance_date', '<=', $request->date_to);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'attendance_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $records = $query->paginate(20);

        // Get filter options
        $classes = ClassModel::all();
        $academicYears = AcademicYear::all();

        // Calculate attendance statistics
        $totalRecords = Attendance::count();
        $presentCount = Attendance::where('status', 'present')->count();
        $absentCount = Attendance::where('status', 'absent')->count();
        $lateCount = Attendance::where('status', 'late')->count();
        $excusedCount = Attendance::where('status', 'excused')->count();

        $averageAttendancePercentage = $totalRecords > 0
            ? round(($presentCount / $totalRecords) * 100, 2)
            : 0;

        return view('admin.reports.attendance-report', compact(
            'records',
            'classes',
            'academicYears',
            'totalRecords',
            'presentCount',
            'absentCount',
            'lateCount',
            'excusedCount',
            'averageAttendancePercentage'
        ));
    }
}

    }
}
