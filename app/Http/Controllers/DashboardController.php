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
        $permissions = $this->dashboardPermissions($request);

        $totalStudents = null;
        $totalTeachers = null;
        $activeClasses = null;
        $attendancePercentage = null;
        $attendanceCount = null;

        if ($permissions['canViewMetrics']) {
            $totalStudents = Student::count();
            $totalTeachers = Teacher::count();
            $activeClasses = ClassModel::count();

            $presentCount = Attendance::where('status', 'present')->count();
            $totalAttendanceRecords = Attendance::count();
            $attendancePercentage = $totalAttendanceRecords > 0
                ? round(($presentCount / $totalAttendanceRecords) * 100, 1)
                : 0;
            $attendanceCount = $presentCount;
        }

        $announcements = [];
        if ($permissions['canViewAnnouncements']) {
            $announcements = Announcement::latest()
                ->take(5)
                ->get()
                ->map(function ($announcement) {
                    return [
                        'title' => $announcement->title ?? 'Announcement',
                        'description' => $announcement->description ?? '',
                        'type' => $announcement->type ?? 'info',
                    ];
                })
                ->toArray();
        }

        $events = [];
        if ($permissions['canViewEvents']) {
            $events = Event::orderBy('event_date')
                ->take(5)
                ->where('status', 'published')
                ->get()
                ->map(function ($event) {
                    $colors = ['primary', 'success', 'warning', 'danger', 'info'];
                    $colorIndex = abs(crc32((string) $event->id)) % count($colors);

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
                        'color' => $colors[$colorIndex],
                    ];
                })
                ->toArray();
        }

        return view('index', array_merge($permissions, [
            'totalStudents' => $totalStudents,
            'totalTeachers' => $totalTeachers,
            'activeClasses' => $activeClasses,
            'attendancePercentage' => $attendancePercentage,
            'attendanceCount' => $attendanceCount,
            'announcements' => $announcements,
            'events' => $events,
        ]));
    }

    /**
     * Get chart data via AJAX
     */
    public function getChartData(Request $request)
    {
        $permissions = $this->dashboardPermissions($request);

        abort_unless(
            $permissions['canViewAttendanceTrend'] || $permissions['canViewEnrollment'] || $permissions['canViewPerformance'],
            403
        );

        $response = [
            'attendanceTrend' => null,
            'enrollment' => null,
            'performance' => null,
        ];

        if ($permissions['canViewAttendanceTrend']) {
            $attendanceTrendLabels = [];
            $attendanceTrendData = [];

            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $total = Attendance::where('attendance_date', $date)->count();
                $present = Attendance::where('attendance_date', $date)
                    ->where('status', 'present')
                    ->count();

                $attendanceTrendData[] = $total > 0 ? round(($present / $total) * 100, 1) : 0;
                $attendanceTrendLabels[] = $date->format('D');
            }

            $response['attendanceTrend'] = [
                'labels' => $attendanceTrendLabels,
                'data' => $attendanceTrendData,
            ];
        }

        if ($permissions['canViewEnrollment']) {
            $enrollmentByClass = ClassModel::withCount('levels')
                ->get()
                ->map(function ($class) {
                    return [
                        'name' => $class->name,
                        'count' => $class->levels_count ?? 0,
                    ];
                });

            $response['enrollment'] = [
                'labels' => $enrollmentByClass->pluck('name')->toArray(),
                'data' => $enrollmentByClass->pluck('count')->toArray(),
            ];
        }

        if ($permissions['canViewPerformance']) {
            $performanceByClass = ClassModel::with('levels.student.results')
                ->get()
                ->map(function ($class) {
                    $studentIds = $class->levels()->pluck('student_id')->unique();
                    if ($studentIds->isEmpty()) {
                        return [
                            'name' => $class->name,
                            'average' => 0,
                        ];
                    }

                    $results = Result::whereIn('student_id', $studentIds)->get();
                    if ($results->isEmpty()) {
                        return [
                            'name' => $class->name,
                            'average' => 0,
                        ];
                    }

                    return [
                        'name' => $class->name,
                        'average' => round(($results->avg('score') ?? 0), 1),
                    ];
                });

            $response['performance'] = [
                'labels' => $performanceByClass->pluck('name')->toArray(),
                'data' => $performanceByClass->pluck('average')->toArray(),
            ];
        }

        return response()->json($response);
    }

    /**
     * Determine dashboard section visibility for the current user.
     */
    private function dashboardPermissions(Request $request): array
    {
        $user = $request->user();

        return [
            'canViewMetrics' => $user ? $user->can('dashboard.metrics.read') : false,
            'canViewAttendanceTrend' => $user ? $user->can('dashboard.attendance_trends.read') : false,
            'canViewEnrollment' => $user ? $user->can('dashboard.enrollment.read') : false,
            'canViewPerformance' => $user ? $user->can('dashboard.performance.read') : false,
            'canViewAnnouncements' => $user ? $user->can('dashboard.announcements.read') : false,
            'canViewEvents' => $user ? $user->can('dashboard.events.read') : false,
        ];
    }
}
