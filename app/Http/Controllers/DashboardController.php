<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\ClassModel;
use App\Models\Attendance;
use App\Models\Result;
use App\Models\Announcement;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with real data
     */
    public function index(Request $request)
    {
        // Get total counts
        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();
        $activeClasses = ClassModel::count();

        // Get overall attendance statistics
        $presentCount = Attendance::where('status', 'present')->count();
        $totalAttendanceRecords = Attendance::count();
        $attendancePercentage = $totalAttendanceRecords > 0
            ? round(($presentCount / $totalAttendanceRecords) * 100, 1)
            : 0;
        $attendanceCount = $presentCount;

        // Student enrollment by class
        $enrollmentByClass = ClassModel::withCount('levels')
            ->get()
            ->map(function ($class) {
                return [
                    'name' => $class->name,
                    'count' => $class->levels_count ?? 0
                ];
            });

        $enrollmentLabels = $enrollmentByClass->pluck('name')->toArray();
        $enrollmentData = $enrollmentByClass->pluck('count')->toArray();

        // Academic Performance (average grades by class)
        $performanceByClass = ClassModel::with('levels.student.results')
            ->get()
            ->map(function ($class) {
                $studentIds = $class->levels()->pluck('student_id')->unique();
                if ($studentIds->isEmpty()) {
                    return [
                        'name' => $class->name,
                        'average' => 0
                    ];
                }

                $results = Result::whereIn('student_id', $studentIds)->get();
                if ($results->isEmpty()) {
                    return [
                        'name' => $class->name,
                        'average' => 0
                    ];
                }

                $averageScore = $results->avg('score') ?? 0;
                return [
                    'name' => $class->name,
                    'average' => round($averageScore, 1)
                ];
            });

        $performanceLabels = $performanceByClass->pluck('name')->toArray();
        $performanceData = $performanceByClass->pluck('average')->toArray();

        // Attendance trend (last 7 days)
        $attendanceTrend = [];
        $attendanceTrendLabels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $total = Attendance::where('attendance_date', $date)->count();
            $present = Attendance::where('attendance_date', $date)
                ->where('status', 'present')
                ->count();
            $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;

            $attendanceTrend[] = $percentage;
            $attendanceTrendLabels[] = $date->format('D');
        }

        // Recent announcements
        $announcements = Announcement::latest()
            ->take(5)
            ->get()
            ->map(function ($announcement) {
                return [
                    'title' => $announcement->title ?? 'Announcement',
                    'description' => $announcement->description ?? '',
                    'type' => $announcement->type ?? 'info'
                ];
            })
            ->toArray();

        // Upcoming events
        $events = Event::orderBy('event_date')
            ->take(5)
            ->where('status', 'published')
            ->get()
            ->map(function ($event) {
                $colors = ['primary', 'success', 'warning', 'danger', 'info'];
                $colorIndex = abs(crc32($event->id)) % count($colors);
                return [
                    'id' => $event->id,
                    'title' => $event->title ?? 'Event',
                    'description' => $event->description ?? 'No description provided',
                    'location' => $event->location ?? 'TBD',
                    'category' => $event->category ?? 'General',
                    'date' => $event->event_date ? Carbon::parse($event->event_date)->format('M d, Y') : '',
                    'date_full' => $event->event_date ? Carbon::parse($event->event_date)->format('l, F j, Y') : '',
                    'start_time' => $event->start_time ? Carbon::parse($event->start_time)->format('g:i A') : 'Not set',
                    'end_time' => $event->end_time ? Carbon::parse($event->end_time)->format('g:i A') : 'Not set',
                    'status' => $event->status ?? 'upcoming',
                    'notes' => $event->notes ?? '',
                    'color' => $colors[$colorIndex]
                ];
            })
            ->toArray();

        // dd(json_encode($attendanceTrend));

        return view('index', [
            'totalStudents' => $totalStudents,
            'totalTeachers' => $totalTeachers,
            'activeClasses' => $activeClasses,
            'attendancePercentage' => $attendancePercentage,
            'attendanceCount' => $attendanceCount,
            'announcements' => $announcements,
            'events' => $events
        ]);
    }

    /**
     * Get chart data via AJAX
     */
    public function getChartData()
    {
        // Student enrollment by class
        $enrollmentByClass = ClassModel::withCount('levels')
            ->get()
            ->map(function ($class) {
                return [
                    'name' => $class->name,
                    'count' => $class->levels_count ?? 0
                ];
            });

        $enrollmentLabels = $enrollmentByClass->pluck('name')->toArray();
        $enrollmentData = $enrollmentByClass->pluck('count')->toArray();

        // Academic Performance (average grades by class)
        $performanceByClass = ClassModel::with('levels.student.results')
            ->get()
            ->map(function ($class) {
                $studentIds = $class->levels()->pluck('student_id')->unique();
                if ($studentIds->isEmpty()) {
                    return [
                        'name' => $class->name,
                        'average' => 0
                    ];
                }

                $results = Result::whereIn('student_id', $studentIds)->get();
                if ($results->isEmpty()) {
                    return [
                        'name' => $class->name,
                        'average' => 0
                    ];
                }

                $averageScore = $results->avg('score') ?? 0;
                return [
                    'name' => $class->name,
                    'average' => round($averageScore, 1)
                ];
            });

        $performanceLabels = $performanceByClass->pluck('name')->toArray();
        $performanceData = $performanceByClass->pluck('average')->toArray();

        // Attendance trend (last 7 days)
        $attendanceTrend = [];
        $attendanceTrendLabels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $total = Attendance::where('attendance_date', $date)->count();
            $present = Attendance::where('attendance_date', $date)
                ->where('status', 'present')
                ->count();
            $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;

            $attendanceTrend[] = $percentage;
            $attendanceTrendLabels[] = $date->format('D');
        }

        return response()->json([
            'enrollmentLabels' => $enrollmentLabels,
            'enrollmentData' => $enrollmentData,
            'performanceLabels' => $performanceLabels,
            'performanceData' => $performanceData,
            'attendanceTrendLabels' => $attendanceTrendLabels,
            'attendanceTrendData' => $attendanceTrend
        ]);
    }
}
