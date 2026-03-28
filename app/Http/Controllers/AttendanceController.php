<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassModel;
use App\Models\AcademicYear;
use App\Models\AcademicPeriod;
use App\Models\LevelData;
use App\Models\Subject;
use App\Models\AssignSubject;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Display attendance taking form
     */
    public function create(Request $request)
    {
        $academicYears = AcademicYear::all();
        $academicPeriods = AcademicPeriod::all();
        $classes = ClassModel::all();
        $subjects = Subject::all();

        return view('admin.attendance.create', compact('academicYears', 'academicPeriods', 'classes', 'subjects'));
    }

    /**
     * Get students for selected class and academic year
     */
    public function getStudents(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:class_models,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'academic_period_id' => 'required|exists:academic_periods,id',
            'attendance_date' => 'required|date',
            'subject_id' => 'nullable|exists:subjects,id',
        ]);

        $classId = $validated['class_id'];
        $academicYearId = $validated['academic_year_id'];
        $academicPeriodId = $validated['academic_period_id'];
        $attendanceDate = $validated['attendance_date'];
        $subjectId = $validated['subject_id'] ?? null;

        // Get students in the selected class and academic year
        $students = LevelData::with('student')
            ->where('class_id', $classId)
            ->where('academic_year_id', $academicYearId)
            ->get();

        // Get existing attendance records for the date
        $query = Attendance::where('class_id', $classId)
            ->where('academic_year_id', $academicYearId)
            ->where('academic_period_id', $academicPeriodId)
            ->where('attendance_date', $attendanceDate);

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        $existingAttendance = $query->get()->keyBy('student_id');

        return response()->json([
            'students' => $students,
            'existingAttendance' => $existingAttendance,
        ]);
    }

    /**
     * Store attendance records
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:class_models,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'academic_period_id' => 'required|exists:academic_periods,id',
            'attendance_date' => 'required|date',
            'subject_id' => 'nullable|exists:subjects,id',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.level_data_id' => 'required|exists:level_data,id',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
            'attendance.*.remarks' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Delete existing attendance for this date, class, year, period, and subject
            $query = Attendance::where('class_id', $validated['class_id'])
                ->where('academic_year_id', $validated['academic_year_id'])
                ->where('academic_period_id', $validated['academic_period_id'])
                ->where('attendance_date', $validated['attendance_date']);

            if ($validated['subject_id']) {
                $query->where('subject_id', $validated['subject_id']);
            }

            $query->delete();

            // Create new attendance records
            foreach ($validated['attendance'] as $record) {
                Attendance::create([
                    'student_id' => $record['student_id'],
                    'level_data_id' => $record['level_data_id'],
                    'class_id' => $validated['class_id'],
                    'academic_year_id' => $validated['academic_year_id'],
                    'academic_period_id' => $validated['academic_period_id'],
                    'subject_id' => $validated['subject_id'] ?? null,
                    'attendance_date' => $validated['attendance_date'],
                    'status' => $record['status'],
                    'remarks' => $record['remarks'] ?? null,
                    'teacher_id' => auth()->user()->id ?? null,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.attendance.create')
                ->with('success', 'Attendance recorded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Error recording attendance: ' . $e->getMessage());
        }
    }

    /**
     * Class attendance report
     */
    public function classReport(Request $request)
    {
        $query = Attendance::with('student', 'classModel', 'academicYear', 'academicPeriod', 'subject');

        $academicYears = AcademicYear::all();
        $academicPeriods = AcademicPeriod::all();
        $classes = ClassModel::all();
        $subjects = Subject::all();

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('academic_period_id')) {
            $query->where('academic_period_id', $request->academic_period_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('date_from')) {
            $query->where('attendance_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('attendance_date', '<=', $request->date_to);
        }

        $records = $query->orderBy('attendance_date', 'desc')->paginate(50);

        // Calculate statistics
        $stats = [
            'total_records' => Attendance::count(),
            'present' => Attendance::where('status', 'present')->count(),
            'absent' => Attendance::where('status', 'absent')->count(),
            'late' => Attendance::where('status', 'late')->count(),
            'excused' => Attendance::where('status', 'excused')->count(),
        ];

        return view('admin.attendance.class-report', compact(
            'records',
            'academicYears',
            'academicPeriods',
            'classes',
            'subjects',
            'stats'
        ));
    }

    /**
     * Subject attendance report
     */
    public function subjectReport(Request $request)
    {
        $query = Attendance::with('subject', 'classModel', 'academicYear', 'academicPeriod', 'student')
            ->whereNotNull('subject_id');

        $academicYears = AcademicYear::all();
        $academicPeriods = AcademicPeriod::all();
        $classes = ClassModel::all();
        $subjects = Subject::all();

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('academic_period_id')) {
            $query->where('academic_period_id', $request->academic_period_id);
        }

        if ($request->filled('date_from')) {
            $query->where('attendance_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('attendance_date', '<=', $request->date_to);
        }

        $records = $query->orderBy('attendance_date', 'desc')->paginate(50);

        // Calculate subject-wise statistics
        $subjectStats = Attendance::whereNotNull('subject_id')
            ->select('subject_id')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count")
            ->groupBy('subject_id')
            ->get();

        return view('admin.attendance.subject-report', compact(
            'records',
            'academicYears',
            'academicPeriods',
            'classes',
            'subjects',
            'subjectStats'
        ));
    }

    /**
     * Student attendance report
     */
    public function studentReport(Request $request)
    {
        $students = Student::with('levelData.classModel', 'levelData.academicYear')->get();

        $query = Attendance::with('classModel', 'academicYear', 'academicPeriod', 'subject');

        $academicYears = AcademicYear::all();
        $academicPeriods = AcademicPeriod::all();
        $selectedStudent = null;
        $selectedAcademicYear = null;

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
            $selectedStudent = Student::find($request->student_id);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
            $selectedAcademicYear = AcademicYear::find($request->academic_year_id);
        }

        if ($request->filled('academic_period_id')) {
            $query->where('academic_period_id', $request->academic_period_id);
        }

        if ($request->filled('date_from')) {
            $query->where('attendance_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('attendance_date', '<=', $request->date_to);
        }

        $records = $query->orderBy('attendance_date', 'desc')->paginate(50);

        // Calculate student attendance statistics
        $stats = null;
        if ($selectedStudent) {
            $totalClasses = Attendance::where('student_id', $selectedStudent->id)->count();
            $presentDays = Attendance::where('student_id', $selectedStudent->id)
                ->present()
                ->count();
            $absentDays = Attendance::where('student_id', $selectedStudent->id)
                ->absent()
                ->count();
            $lateDays = Attendance::where('student_id', $selectedStudent->id)
                ->where('status', 'late')
                ->count();

            $attendancePercentage = $totalClasses > 0
                ? round(($presentDays / $totalClasses) * 100, 2)
                : 0;

            $stats = [
                'total_classes' => $totalClasses,
                'present' => $presentDays,
                'absent' => $absentDays,
                'late' => $lateDays,
                'attendance_percentage' => $attendancePercentage,
            ];
        }

        return view('admin.attendance.student-report', compact(
            'records',
            'students',
            'academicYears',
            'academicPeriods',
            'selectedStudent',
            'selectedAcademicYear',
            'stats'
        ));
    }

    /**
     * Attendance analytics dashboard
     */
    public function analytics(Request $request)
    {
        $academicYears = AcademicYear::all();
        $classes = ClassModel::all();
        $subjects = Subject::all();

        // Determine date range filter
        $dateFrom = now()->subDays(30)->startOfDay();
        $dateTo = now()->endOfDay();

        // Check if custom date range is provided
        if ($request->has('date_from') && $request->filled('date_from')) {
            $dateFrom = \Carbon\Carbon::createFromFormat('Y-m-d', $request->get('date_from'))->startOfDay();
        }
        if ($request->has('date_to') && $request->filled('date_to')) {
            $dateTo = \Carbon\Carbon::createFromFormat('Y-m-d', $request->get('date_to'))->endOfDay();
        }

        // Base query with date filter
        $baseQuery = Attendance::whereBetween('attendance_date', [$dateFrom, $dateTo]);

        // Apply class filter if provided
        if ($request->has('class_id') && $request->filled('class_id')) {
            $baseQuery = $baseQuery->where('class_id', $request->get('class_id'));
        }

        // Apply subject filter if provided
        if ($request->has('subject_id') && $request->filled('subject_id')) {
            $baseQuery = $baseQuery->where('subject_id', $request->get('subject_id'));
        }

        // Overall statistics
        $totalAttendanceRecords = $baseQuery->count();
        $presentCount = (clone $baseQuery)->where('status', 'present')->count();
        $absentCount = (clone $baseQuery)->where('status', 'absent')->count();
        $lateCount = (clone $baseQuery)->where('status', 'late')->count();
        $excusedCount = (clone $baseQuery)->where('status', 'excused')->count();

        $overallPercentage = $totalAttendanceRecords > 0
            ? round(($presentCount / $totalAttendanceRecords) * 100, 2)
            : 0;

        // Class-wise attendance statistics
        $classStats = (clone $baseQuery)->select('class_id')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count")
            ->groupBy('class_id')
            ->with('classModel')
            ->get()
            ->map(function ($stat) {
                return [
                    'class' => $stat->classModel->name ?? 'N/A',
                    'total' => $stat->total,
                    'present' => $stat->present_count,
                    'percentage' => $stat->total > 0 ? round(($stat->present_count / $stat->total) * 100, 1) : 0,
                ];
            });

        // Subject-wise attendance statistics
        $subjectStats = (clone $baseQuery)->whereNotNull('subject_id')
            ->select('subject_id')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count")
            ->groupBy('subject_id')
            ->get()
            ->map(function ($stat) {
                $subject = Subject::find($stat->subject_id);
                return [
                    'subject' => $subject->name ?? 'N/A',
                    'total' => $stat->total,
                    'present' => $stat->present_count,
                    'percentage' => $stat->total > 0 ? round(($stat->present_count / $stat->total) * 100, 1) : 0,
                ];
            });

        // Daily attendance trend
        $dailyTrend = (clone $baseQuery)->select('attendance_date')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count")
            ->groupBy('attendance_date')
            ->orderBy('attendance_date')
            ->get()
            ->map(function ($record) {
                return [
                    'date' => $record->attendance_date->format('M d'),
                    'total' => $record->total,
                    'present' => $record->present_count,
                    'percentage' => $record->total > 0 ? round(($record->present_count / $record->total) * 100, 1) : 0,
                ];
            });

        // Status distribution
        $statusDistribution = [
            'present' => $presentCount,
            'absent' => $absentCount,
            'late' => $lateCount,
            'excused' => $excusedCount,
        ];

        // Top 10 most absent students (filtered by date range)
        $mostAbsentStudents = LevelData::with('student', 'classModel')
            ->get()
            ->map(function ($levelData) use ($baseQuery) {
                $student = $levelData->student;
                $totalAttendance = (clone $baseQuery)->where('student_id', $student->id)->count();
                $absences = (clone $baseQuery)->where('student_id', $student->id)
                    ->where('status', 'absent')
                    ->count();

                return [
                    'name' => $student->first_name . ' ' . $student->last_name,
                    'student_id' => $student->student_id,
                    'class' => $levelData->classModel->name ?? 'N/A',
                    'total_attendance' => $totalAttendance,
                    'absences' => $absences,
                    'absence_percentage' => $totalAttendance > 0 ? round(($absences / $totalAttendance) * 100, 1) : 0,
                ];
            })
            ->filter(function ($item) {
                return $item['total_attendance'] > 0;
            })
            ->sortByDesc('absence_percentage')
            ->take(10)
            ->values();

        return view('admin.attendance.analytics', compact(
            'totalAttendanceRecords',
            'presentCount',
            'absentCount',
            'lateCount',
            'excusedCount',
            'overallPercentage',
            'classStats',
            'subjectStats',
            'dailyTrend',
            'statusDistribution',
            'mostAbsentStudents',
            'academicYears',
            'classes',
            'subjects'
        ));
    }
}
