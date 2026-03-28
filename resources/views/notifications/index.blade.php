@extends('layouts.app')

@section('title', 'Notifications')

@push('styles')
<style>
    .notifications-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2.5rem 2rem;
        border-radius: 10px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .notifications-header h2 {
        font-weight: 700;
        margin-bottom: 0.5rem;
        font-size: 1.75rem;
    }

    .notifications-header p {
        opacity: 0.95;
        margin: 0;
        font-size: 0.95rem;
    }

    .notification-stats {
        display: flex;
        gap: 2rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .notification-stat {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .notification-stat-number {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .notification-stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .notification-filters {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 0.5rem 1rem;
        border: 2px solid #e9ecef;
        background: white;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .filter-btn:hover {
        border-color: #667eea;
        color: #667eea;
    }

    .filter-btn.active {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .notification-tabs {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
        border-bottom: 2px solid #e9ecef;
    }

    .notification-tab {
        padding: 1rem 0;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
        font-weight: 500;
        color: #6c757d;
    }

    .notification-tab:hover {
        color: #667eea;
    }

    .notification-tab.active {
        color: #667eea;
        border-bottom-color: #667eea;
    }

    .notification-container {
        display: none;
    }

    .notification-container.active {
        display: block;
    }

    .notification-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .notification-card:hover {
        border-color: #667eea;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
        transform: translateY(-2px);
    }

    .notification-card.event {
        border-left: 4px solid #667eea;
    }

    .notification-card.announcement {
        border-left: 4px solid #28a745;
    }

    .notification-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .notification-title-group {
        flex: 1;
    }

    .notification-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #212529;
        margin: 0 0 0.25rem 0;
    }

    .notification-meta {
        display: flex;
        gap: 1.5rem;
        margin: 0.5rem 0 0 0;
        flex-wrap: wrap;
    }

    .notification-meta-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.85rem;
        color: #6c757d;
    }

    .notification-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge-new {
        background: #fff3cd;
        color: #856404;
    }

    .badge-upcoming {
        background: #d1ecf1;
        color: #0c5460;
    }

    .badge-event {
        background: #e7f3ff;
        color: #0d6efd;
    }

    .badge-announcement {
        background: #d1fae5;
        color: #065f46;
    }

    .notification-description {
        color: #495057;
        line-height: 1.5;
        margin: 0.75rem 0;
    }

    .notification-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e9ecef;
    }

    .notification-date {
        font-size: 0.85rem;
        color: #999;
    }

    .notification-organizer {
        font-size: 0.85rem;
        color: #6c757d;
    }

    .notification-expandable-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease, opacity 0.3s ease;
        opacity: 0;
        margin-top: 0;
    }

    .notification-card.expanded .notification-expandable-content {
        max-height: 500px;
        opacity: 1;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e9ecef;
    }

    .expandable-section {
        margin-bottom: 1rem;
    }

    .expandable-section:last-child {
        margin-bottom: 0;
    }

    .expandable-section-title {
        font-weight: 600;
        font-size: 0.9rem;
        color: #212529;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .expandable-section-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .expandable-detail {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .expandable-detail-icon {
        color: #6c757d;
        font-size: 0.9rem;
        margin-top: 0.15rem;
        flex-shrink: 0;
    }

    .expandable-detail-text {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .expandable-detail-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #999;
        font-weight: 600;
    }

    .expandable-detail-value {
        color: #212529;
        font-size: 0.9rem;
    }

    .notification-notes {
        padding: 0.75rem;
        background-color: #f8f9fa;
        border-radius: 4px;
        border-left: 3px solid #0d6efd;
    }

    .notification-notes p {
        margin: 0;
        color: #495057;
        font-size: 0.85rem;
    }

    .toggle-expand-icon {
        transition: transform 0.3s ease;
        color: #6c757d;
    }

    .notification-card.expanded .toggle-expand-icon {
        transform: rotate(180deg);
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #999;
    }

    .empty-state-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .empty-state-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #495057;
    }

    .empty-state-description {
        color: #999;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .notifications-header {
            padding: 2rem 1.5rem;
        }

        .notifications-header h2 {
            font-size: 1.5rem;
        }

        .notification-stats {
            gap: 1rem;
        }

        .notification-stats .notification-stat-number {
            font-size: 1.25rem;
        }

        .notification-filters {
            flex-direction: column;
        }

        .filter-btn {
            width: 100%;
        }

        .notification-tabs {
            flex-wrap: wrap;
            gap: 0;
        }

        .notification-tab {
            padding: 0.75rem 1rem;
        }

        .expandable-section-content {
            grid-template-columns: 1fr;
        }

        .notification-card {
            padding: 1rem;
        }

        .notification-footer {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
    }
</style>
@endpush

@section('content')
<!-- Notifications Header -->
<div class="notifications-header">
    <h2>🔔 Notifications</h2>
    <p>Stay updated with upcoming events and important announcements</p>

    <div class="notification-stats">
        <div class="notification-stat">
            <span class="notification-stat-number">{{ $totalNotifications }}</span>
            <span class="notification-stat-label">Total Notifications</span>
        </div>
        <div class="notification-stat">
            <span class="notification-stat-number">{{ $upcomingEventCount }}</span>
            <span class="notification-stat-label">Upcoming Events</span>
        </div>
        <div class="notification-stat">
            <span class="notification-stat-number">{{ $newAnnouncementCount }}</span>
            <span class="notification-stat-label">New Announcements</span>
        </div>
    </div>
</div>

<!-- Filter Buttons -->
<div class="notification-filters">
    <button class="filter-btn active" data-filter="all">All Notifications</button>
    <button class="filter-btn" data-filter="events">Events Only</button>
    <button class="filter-btn" data-filter="announcements">Announcements Only</button>
</div>

<!-- Tabs -->
<div class="notification-tabs">
    <div class="notification-tab active" data-tab="all-tab">All</div>
    <div class="notification-tab" data-tab="events-tab">
        Upcoming Events
        @if($upcomingEventCount > 0)
        <span class="badge badge-upcoming ms-2">{{ $upcomingEventCount }}</span>
        @endif
    </div>
    <div class="notification-tab" data-tab="announcements-tab">
        Announcements
        @if($newAnnouncementCount > 0)
        <span class="badge badge-new ms-2">{{ $newAnnouncementCount }}</span>
        @endif
    </div>
</div>

<!-- All Notifications -->
<div id="all-tab" class="notification-container active">
    @if(count($events) > 0 || count($announcements) > 0)
    @php
    $combined = array_merge($events, $announcements);
    usort($combined, function($a, $b) {
    $dateA = $a['type'] === 'event' ? $a['created_at'] : $a['created_at'];
    $dateB = $b['type'] === 'event' ? $b['created_at'] : $b['created_at'];
    return $dateB->getTimestamp() - $dateA->getTimestamp();
    });
    @endphp

    @foreach($combined as $notification)
    @if($notification['type'] === 'event')
    <!-- Event Card -->
    <div class="notification-card event" data-type="event">
        <div class="notification-header">
            <div class="notification-title-group">
                <h5 class="notification-title">{{ $notification['title'] }}</h5>
                <div class="notification-meta">
                    <span class="notification-meta-item">
                        <i data-lucide="calendar" style="width: 14px; height: 14px;"></i>
                        {{ $notification['date'] }}
                    </span>
                    <span class="notification-meta-item">
                        <i data-lucide="map-pin" style="width: 14px; height: 14px;"></i>
                        {{ $notification['location'] }}
                    </span>
                </div>
            </div>
            <div style="display: flex; gap: 0.5rem; flex-shrink: 0;">
                @if($notification['is_new'])
                <span class="notification-badge badge-new">
                    <i data-lucide="star" style="width: 12px; height: 12px;"></i>
                    New
                </span>
                @endif
                @if($notification['is_upcoming'])
                <span class="notification-badge badge-upcoming">
                    <i data-lucide="alert-circle" style="width: 12px; height: 12px;"></i>
                    Upcoming
                </span>
                @endif
                <i class="toggle-expand-icon" data-lucide="chevron-down" style="width: 20px; height: 20px;"></i>
            </div>
        </div>

        <p class="notification-description">{{ $notification['description'] }}</p>

        <div class="notification-expandable-content">
            <div class="expandable-section">
                <div class="expandable-section-title">
                    <i data-lucide="clock" style="width: 16px; height: 16px;"></i>
                    Time Details
                </div>
                <div class="expandable-section-content">
                    <div class="expandable-detail">
                        <div class="expandable-detail-icon">
                            <i data-lucide="clock" style="width: 16px; height: 16px;"></i>
                        </div>
                        <div class="expandable-detail-text">
                            <span class="expandable-detail-label">Start Time</span>
                            <span class="expandable-detail-value">{{ $notification['start_time'] }}</span>
                        </div>
                    </div>
                    <div class="expandable-detail">
                        <div class="expandable-detail-icon">
                            <i data-lucide="clock" style="width: 16px; height: 16px;"></i>
                        </div>
                        <div class="expandable-detail-text">
                            <span class="expandable-detail-label">End Time</span>
                            <span class="expandable-detail-value">{{ $notification['end_time'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="expandable-section">
                <div class="expandable-section-title">
                    <i data-lucide="info" style="width: 16px; height: 16px;"></i>
                    Event Details
                </div>
                <div class="expandable-section-content">
                    <div class="expandable-detail">
                        <div class="expandable-detail-icon">
                            <i data-lucide="tag" style="width: 16px; height: 16px;"></i>
                        </div>
                        <div class="expandable-detail-text">
                            <span class="expandable-detail-label">Category</span>
                            <span class="expandable-detail-value">{{ $notification['category'] }}</span>
                        </div>
                    </div>
                    <div class="expandable-detail">
                        <div class="expandable-detail-icon">
                            <i data-lucide="info" style="width: 16px; height: 16px;"></i>
                        </div>
                        <div class="expandable-detail-text">
                            <span class="expandable-detail-label">Status</span>
                            <span class="expandable-detail-value" style="text-transform: capitalize;">{{ $notification['status'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if($notification['notes'])
            <div class="expandable-section">
                <div class="notification-notes">
                    <p><strong>Notes:</strong> {{ $notification['notes'] }}</p>
                </div>
            </div>
            @endif
        </div>

        <div class="notification-footer">
            <span class="notification-date">{{ $notification['created_at']->format('M d, Y H:i') }}</span>
            <span class="notification-organizer">Organized by {{ $notification['organizer'] }}</span>
        </div>
    </div>
    @else
    <!-- Announcement Card -->
    <div class="notification-card announcement" data-type="announcement">
        <div class="notification-header">
            <div class="notification-title-group">
                <h5 class="notification-title">{{ $notification['title'] }}</h5>
            </div>
            <div style="display: flex; gap: 0.5rem; flex-shrink: 0;">
                @if($notification['is_new'])
                <span class="notification-badge badge-new">
                    <i data-lucide="star" style="width: 12px; height: 12px;"></i>
                    New
                </span>
                @endif
                <span class="notification-badge" style="background: var(--bs-{{ $notification['badge_color'] }}, #e7f3ff); color: var(--bs-{{ $notification['badge_color'] }}-text, #0d6efd);">
                    {{ ucfirst($notification['badge_type']) }}
                </span>
                <i class="toggle-expand-icon" data-lucide="chevron-down" style="width: 20px; height: 20px;"></i>
            </div>
        </div>

        <p class="notification-description">{{ $notification['description'] }}</p>

        <div class="notification-footer">
            <span class="notification-date">{{ $notification['created_at_formatted'] }}</span>
            <span class="notification-organizer">Posted by {{ $notification['posted_by'] }}</span>
        </div>
    </div>
    @endif
    @endforeach
    @else
    <div class="empty-state">
        <div class="empty-state-icon">📭</div>
        <div class="empty-state-title">No Notifications</div>
        <div class="empty-state-description">There are no events or announcements at the moment</div>
    </div>
    @endif
</div>

<!-- Events Tab -->
<div id="events-tab" class="notification-container">
    @if(count($events) > 0)
    @foreach($events as $event)
    <div class="notification-card event" data-type="event">
        <div class="notification-header">
            <div class="notification-title-group">
                <h5 class="notification-title">{{ $event['title'] }}</h5>
                <div class="notification-meta">
                    <span class="notification-meta-item">
                        <i data-lucide="calendar" style="width: 14px; height: 14px;"></i>
                        {{ $event['date'] }}
                    </span>
                    <span class="notification-meta-item">
                        <i data-lucide="map-pin" style="width: 14px; height: 14px;"></i>
                        {{ $event['location'] }}
                    </span>
                </div>
            </div>
            <div style="display: flex; gap: 0.5rem; flex-shrink: 0;">
                @if($event['is_new'])
                <span class="notification-badge badge-new">
                    <i data-lucide="star" style="width: 12px; height: 12px;"></i>
                    New
                </span>
                @endif
                @if($event['is_upcoming'])
                <span class="notification-badge badge-upcoming">
                    <i data-lucide="alert-circle" style="width: 12px; height: 12px;"></i>
                    Upcoming
                </span>
                @endif
                <i class="toggle-expand-icon" data-lucide="chevron-down" style="width: 20px; height: 20px;"></i>
            </div>
        </div>

        <p class="notification-description">{{ $event['description'] }}</p>

        <div class="notification-expandable-content">
            <div class="expandable-section">
                <div class="expandable-section-title">
                    <i data-lucide="clock" style="width: 16px; height: 16px;"></i>
                    Time Details
                </div>
                <div class="expandable-section-content">
                    <div class="expandable-detail">
                        <div class="expandable-detail-icon">
                            <i data-lucide="clock" style="width: 16px; height: 16px;"></i>
                        </div>
                        <div class="expandable-detail-text">
                            <span class="expandable-detail-label">Start Time</span>
                            <span class="expandable-detail-value">{{ $event['start_time'] }}</span>
                        </div>
                    </div>
                    <div class="expandable-detail">
                        <div class="expandable-detail-icon">
                            <i data-lucide="clock" style="width: 16px; height: 16px;"></i>
                        </div>
                        <div class="expandable-detail-text">
                            <span class="expandable-detail-label">End Time</span>
                            <span class="expandable-detail-value">{{ $event['end_time'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="expandable-section">
                <div class="expandable-section-title">
                    <i data-lucide="info" style="width: 16px; height: 16px;"></i>
                    Event Details
                </div>
                <div class="expandable-section-content">
                    <div class="expandable-detail">
                        <div class="expandable-detail-icon">
                            <i data-lucide="tag" style="width: 16px; height: 16px;"></i>
                        </div>
                        <div class="expandable-detail-text">
                            <span class="expandable-detail-label">Category</span>
                            <span class="expandable-detail-value">{{ $event['category'] }}</span>
                        </div>
                    </div>
                    <div class="expandable-detail">
                        <div class="expandable-detail-icon">
                            <i data-lucide="info" style="width: 16px; height: 16px;"></i>
                        </div>
                        <div class="expandable-detail-text">
                            <span class="expandable-detail-label">Status</span>
                            <span class="expandable-detail-value" style="text-transform: capitalize;">{{ $event['status'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if($event['notes'])
            <div class="expandable-section">
                <div class="notification-notes">
                    <p><strong>Notes:</strong> {{ $event['notes'] }}</p>
                </div>
            </div>
            @endif
        </div>

        <div class="notification-footer">
            <span class="notification-date">{{ $event['created_at']->format('M d, Y H:i') }}</span>
            <span class="notification-organizer">Organized by {{ $event['organizer'] }}</span>
        </div>
    </div>
    @endforeach
    @else
    <div class="empty-state">
        <div class="empty-state-icon">📅</div>
        <div class="empty-state-title">No Upcoming Events</div>
        <div class="empty-state-description">There are no scheduled events at the moment</div>
    </div>
    @endif
</div>

<!-- Announcements Tab -->
<div id="announcements-tab" class="notification-container">
    @if(count($announcements) > 0)
    @foreach($announcements as $announcement)
    <div class="notification-card announcement" data-type="announcement">
        <div class="notification-header">
            <div class="notification-title-group">
                <h5 class="notification-title">{{ $announcement['title'] }}</h5>
            </div>
            <div style="display: flex; gap: 0.5rem; flex-shrink: 0;">
                @if($announcement['is_new'])
                <span class="notification-badge badge-new">
                    <i data-lucide="star" style="width: 12px; height: 12px;"></i>
                    New
                </span>
                @endif
                <span class="notification-badge" style="background: var(--bs-{{ $announcement['badge_color'] }}, #e7f3ff); color: var(--bs-{{ $announcement['badge_color'] }}-text, #0d6efd);">
                    {{ ucfirst($announcement['badge_type']) }}
                </span>
                <i class="toggle-expand-icon" data-lucide="chevron-down" style="width: 20px; height: 20px;"></i>
            </div>
        </div>

        <p class="notification-description">{{ $announcement['description'] }}</p>

        <div class="notification-footer">
            <span class="notification-date">{{ $announcement['created_at_formatted'] }}</span>
            <span class="notification-organizer">Posted by {{ $announcement['posted_by'] }}</span>
        </div>
    </div>
    @endforeach
    @else
    <div class="empty-state">
        <div class="empty-state-icon">📢</div>
        <div class="empty-state-title">No Announcements</div>
        <div class="empty-state-description">There are no announcements at the moment</div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab switching functionality
        const tabs = document.querySelectorAll('.notification-tab');
        const containers = document.querySelectorAll('.notification-container');

        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');

                // Remove active class from all tabs and containers
                tabs.forEach(t => t.classList.remove('active'));
                containers.forEach(c => c.classList.remove('active'));

                // Add active class to clicked tab and corresponding container
                this.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });

        // Filter buttons
        const filterBtns = document.querySelectorAll('.filter-btn');
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const filter = this.getAttribute('data-filter');
                filterNotifications(filter);
            });
        });

        // Card expansion functionality
        const cards = document.querySelectorAll('.notification-card');
        cards.forEach(card => {
            const header = card.querySelector('.notification-header');

            header.addEventListener('click', function(e) {
                e.preventDefault();
                card.classList.toggle('expanded');

                // Reinitialize lucide icons if needed
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            });
        });
    });

    function filterNotifications(filter) {
        const cards = document.querySelectorAll('.notification-card');
        cards.forEach(card => {
            if (filter === 'all') {
                card.style.display = 'block';
            } else {
                const type = card.getAttribute('data-type');
                card.style.display = type === filter ? 'block' : 'none';
            }
        });
    }
</script>
@endpush