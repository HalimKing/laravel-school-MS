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

            \App\Helpers\SystemLogHelper::log('Record Attendance', 'Attendance', "Attendance recorded for class ID: {$validated['class_id']} on {$validated['attendance_date']}");

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
        $classes = ClassModel::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('admin.attendance.analytics', compact('classes', 'subjects'));
    }

    /**
     * Attendance analytics data endpoint (AJAX)
     */
    public function analyticsData(Request $request)
    {
        $filters = $request->validate([
            'class_id' => 'nullable|exists:class_models,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $dateFrom = isset($filters['date_from'])
            ? \Carbon\Carbon::parse($filters['date_from'])->startOfDay()
            : now()->subDays(30)->startOfDay();

        $dateTo = isset($filters['date_to'])
            ? \Carbon\Carbon::parse($filters['date_to'])->endOfDay()
            : now()->endOfDay();

        $baseQuery = $this->buildAnalyticsBaseQuery($filters, $dateFrom, $dateTo);

        $overall = (clone $baseQuery)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count")
            ->selectRaw("SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count")
            ->selectRaw("SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_count")
            ->selectRaw("SUM(CASE WHEN status = 'excused' THEN 1 ELSE 0 END) as excused_count")
            ->first();

        $totalAttendanceRecords = (int) ($overall->total ?? 0);
        $presentCount = (int) ($overall->present_count ?? 0);
        $absentCount = (int) ($overall->absent_count ?? 0);
        $lateCount = (int) ($overall->late_count ?? 0);
        $excusedCount = (int) ($overall->excused_count ?? 0);
        $overallPercentage = $totalAttendanceRecords > 0
            ? round(($presentCount / $totalAttendanceRecords) * 100, 2)
            : 0;

        $classStats = (clone $baseQuery)
            ->join('class_models', 'attendance.class_id', '=', 'class_models.id')
            ->selectRaw('class_models.name as class')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN attendance.status = 'present' THEN 1 ELSE 0 END) as present_count")
            ->groupBy('class_models.name')
            ->orderBy('class_models.name')
            ->get()
            ->map(function ($stat) {
                $total = (int) $stat->total;
                $present = (int) $stat->present_count;
                return [
                    'class' => $stat->class,
                    'total' => $total,
                    'present' => $present,
                    'absent' => $total - $present,
                    'late' => 0,
                    'percentage' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
                ];
            })
            ->values();

        $subjectStats = (clone $baseQuery)
            ->leftJoin('subjects', 'attendance.subject_id', '=', 'subjects.id')
            ->whereNotNull('attendance.subject_id')
            ->selectRaw("COALESCE(subjects.name, 'N/A') as subject")
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN attendance.status = 'present' THEN 1 ELSE 0 END) as present_count")
            ->groupBy('subjects.name')
            ->orderBy('subjects.name')
            ->get()
            ->map(function ($stat) {
                $total = (int) $stat->total;
                $present = (int) $stat->present_count;
                return [
                    'subject' => $stat->subject,
                    'total' => $total,
                    'present' => $present,
                    'absent' => $total - $present,
                    'late' => 0,
                    'percentage' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
                ];
            })
            ->values();

        $dailyTrend = (clone $baseQuery)
            ->selectRaw('attendance_date')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count")
            ->groupBy('attendance_date')
            ->orderBy('attendance_date')
            ->get()
            ->map(function ($record) {
                $total = (int) $record->total;
                $present = (int) $record->present_count;
                return [
                    'date' => \Carbon\Carbon::parse($record->attendance_date)->format('M d'),
                    'total' => $total,
                    'present' => $present,
                    'percentage' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
                ];
            })
            ->values();

        $mostAbsentStudents = (clone $baseQuery)
            ->join('students', 'attendance.student_id', '=', 'students.id')
            ->leftJoin('class_models', 'attendance.class_id', '=', 'class_models.id')
            ->selectRaw("CONCAT(students.first_name, ' ', students.last_name) as name")
            ->selectRaw('students.student_id as student_ref')
            ->selectRaw("COALESCE(class_models.name, 'N/A') as class")
            ->selectRaw('COUNT(*) as total_attendance')
            ->selectRaw("SUM(CASE WHEN attendance.status = 'absent' THEN 1 ELSE 0 END) as absences")
            ->groupBy('students.id', 'students.first_name', 'students.last_name', 'students.student_id', 'class_models.name')
            ->havingRaw('COUNT(*) > 0')
            ->orderByRaw("(SUM(CASE WHEN attendance.status = 'absent' THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) DESC")
            ->limit(10)
            ->get()
            ->map(function ($student) {
                $total = (int) $student->total_attendance;
                $absences = (int) $student->absences;
                return [
                    'name' => $student->name,
                    'student_id' => $student->student_ref,
                    'class' => $student->class,
                    'total_attendance' => $total,
                    'absences' => $absences,
                    'absence_percentage' => $total > 0 ? round(($absences / $total) * 100, 1) : 0,
                ];
            })
            ->values();

        return response()->json([
            'filters' => [
                'class_id' => $filters['class_id'] ?? null,
                'subject_id' => $filters['subject_id'] ?? null,
                'date_from' => $dateFrom->format('Y-m-d'),
                'date_to' => $dateTo->format('Y-m-d'),
            ],
            'summary' => [
                'totalAttendanceRecords' => $totalAttendanceRecords,
                'presentCount' => $presentCount,
                'absentCount' => $absentCount,
                'lateCount' => $lateCount,
                'excusedCount' => $excusedCount,
                'overallPercentage' => $overallPercentage,
                'classCount' => $classStats->count(),
                'subjectCount' => $subjectStats->count(),
            ],
            'statusDistribution' => [
                'present' => $presentCount,
                'absent' => $absentCount,
                'late' => $lateCount,
                'excused' => $excusedCount,
            ],
            'dailyTrend' => $dailyTrend,
            'classStats' => $classStats,
            'subjectStats' => $subjectStats,
            'mostAbsentStudents' => $mostAbsentStudents,
        ]);
    }

    private function buildAnalyticsBaseQuery(array $filters, \Carbon\Carbon $dateFrom, \Carbon\Carbon $dateTo)
    {
        $query = Attendance::query()->whereBetween('attendance_date', [$dateFrom, $dateTo]);

        if (!empty($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }

        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        return $query;
    }
}
