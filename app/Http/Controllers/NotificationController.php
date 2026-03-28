<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display all notifications (Events and Announcements)
     */
    public function index(Request $request)
    {
        // Get all upcoming events
        $events = Event::orderBy('event_date', 'desc')
            ->with('user')
            ->get()
            ->map(function ($event) {
                $colors = ['primary', 'success', 'warning', 'danger', 'info'];
                $colorIndex = abs(crc32($event->id)) % count($colors);

                $isUpcoming = Carbon::parse($event->event_date)->isFuture();

                return [
                    'id' => $event->id,
                    'type' => 'event',
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
                    'is_new' => $event->created_at->diffInDays(now()) <= 2,
                    'is_upcoming' => $isUpcoming,
                    'created_at' => $event->created_at,
                    'organizer' => $event->user ? $event->user->name : 'Admin'
                ];
            })
            ->toArray();

        // Get all announcements
        $announcements = Announcement::orderBy('created_at', 'desc')
            ->with('user')
            ->get()
            ->map(function ($announcement) {
                $badgeColors = [
                    'info' => 'primary',
                    'success' => 'success',
                    'warning' => 'warning',
                    'error' => 'danger'
                ];

                return [
                    'id' => $announcement->id,
                    'type' => 'announcement',
                    'title' => $announcement->title ?? 'Announcement',
                    'description' => $announcement->description ?? '',
                    'badge_type' => $announcement->type ?? 'info',
                    'badge_color' => $badgeColors[$announcement->type ?? 'info'] ?? 'primary',
                    'is_new' => $announcement->created_at->diffInDays(now()) <= 1,
                    'created_at' => $announcement->created_at,
                    'created_at_formatted' => $announcement->created_at->format('M d, Y'),
                    'posted_by' => $announcement->user ? $announcement->user->name : 'Admin'
                ];
            })
            ->toArray();

        // Get counts
        $newEventCount = count(array_filter($events, fn($event) => $event['is_new']));
        $newAnnouncementCount = count(array_filter($announcements, fn($ann) => $ann['is_new']));
        $upcomingEventCount = count(array_filter($events, fn($event) => $event['is_upcoming']));

        return view('notifications.index', [
            'events' => $events,
            'announcements' => $announcements,
            'newEventCount' => $newEventCount,
            'newAnnouncementCount' => $newAnnouncementCount,
            'upcomingEventCount' => $upcomingEventCount,
            'totalNotifications' => count($events) + count($announcements)
        ]);
    }

    /**
     * Get notifications via AJAX (for updates)
     */
    public function getNotifications(Request $request)
    {
        $type = $request->query('type', 'all'); // 'all', 'events', 'announcements'

        $events = [];
        $announcements = [];

        if ($type === 'all' || $type === 'events') {
            $events = Event::orderBy('event_date', 'desc')
                ->take(10)
                ->get()
                ->map(function ($event) {
                    $colors = ['primary', 'success', 'warning', 'danger', 'info'];
                    $colorIndex = abs(crc32($event->id)) % count($colors);

                    return [
                        'id' => $event->id,
                        'type' => 'event',
                        'title' => $event->title,
                        'date' => $event->event_date ? Carbon::parse($event->event_date)->format('M d, Y') : '',
                        'color' => $colors[$colorIndex],
                        'is_upcoming' => Carbon::parse($event->event_date)->isFuture()
                    ];
                })
                ->toArray();
        }

        if ($type === 'all' || $type === 'announcements') {
            $announcements = Announcement::orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->map(function ($announcement) {
                    return [
                        'id' => $announcement->id,
                        'type' => 'announcement',
                        'title' => $announcement->title,
                        'created_at' => $announcement->created_at->format('M d, Y')
                    ];
                })
                ->toArray();
        }

        return response()->json([
            'events' => $events,
            'announcements' => $announcements,
            'total' => count($events) + count($announcements)
        ]);
    }
}
