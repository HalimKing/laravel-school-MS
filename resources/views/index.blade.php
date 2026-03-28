@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    .chart-container {
        position: relative;
        height: 300px;
    }

    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 10px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .dashboard-header h4 {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .dashboard-header p {
        opacity: 0.9;
        margin: 0;
    }

    .stat-card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .stat-card .card-body {
        padding: 1.5rem;
    }

    .chart-card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .chart-card .card-header {
        background-color: transparent;
        border-bottom: 1px solid #e9ecef;
        padding: 1.25rem;
    }

    .announcement-event-card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        margin: 0.5rem 0;
    }

    .stats-section {
        margin-bottom: 2rem;
    }

    /* Skeleton Loader Styles */
    .skeleton-loader {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 2s infinite;
        border-radius: 4px;
    }

    @keyframes shimmer {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
    }

    .skeleton-chart {
        height: 350px;
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .skeleton-line {
        height: 12px;
        margin-bottom: 8px;
    }

    .skeleton-line.short {
        width: 60%;
    }

    .chart-body-loading {
        padding: 1.5rem;
    }

    /* Collapsible Event Styles */
    .event-accordion {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .event-item {
        border-bottom: 1px solid #e9ecef;
        transition: background-color 0.3s ease;
    }

    .event-item:last-child {
        border-bottom: none;
    }

    .event-item:hover {
        background-color: #f8f9fa;
    }

    .event-header {
        cursor: pointer;
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        user-select: none;
        transition: all 0.3s ease;
        border-left: 4px solid;
    }

    .event-header:active {
        outline: none;
    }

    .event-header-left {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .event-color-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .event-header-content {
        flex: 1;
    }

    .event-title {
        font-weight: 600;
        margin: 0 0 0.25rem 0;
        color: #212529;
        font-size: 0.95rem;
    }

    .event-meta {
        display: flex;
        gap: 1.5rem;
        margin: 0;
        color: #6c757d;
        font-size: 0.85rem;
    }

    .event-meta-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .event-toggle-icon {
        transition: transform 0.3s ease;
        flex-shrink: 0;
        color: #6c757d;
    }

    .event-item.expanded .event-toggle-icon {
        transform: rotate(180deg);
    }

    .event-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease, padding 0.3s ease, opacity 0.3s ease;
        opacity: 0;
        padding: 0 1.5rem;
    }

    .event-item.expanded .event-content {
        max-height: 500px;
        opacity: 1;
        padding: 0 1.5rem 1.25rem 1.5rem;
    }

    .event-description {
        color: #495057;
        font-size: 0.9rem;
        line-height: 1.5;
        margin: 0 0 1rem 0;
    }

    .event-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .event-detail-item {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .event-detail-icon {
        color: #6c757d;
        font-size: 0.9rem;
        margin-top: 0.15rem;
        flex-shrink: 0;
    }

    .event-detail-text {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .event-detail-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #999;
        font-weight: 600;
    }

    .event-detail-value {
        color: #212529;
        font-size: 0.9rem;
    }

    .event-status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-top: 0.5rem;
    }

    .event-status-upcoming {
        background-color: #e7f3ff;
        color: #0d6efd;
    }

    .event-status-published {
        background-color: #d1fae5;
        color: #059669;
    }

    .event-status-cancelled {
        background-color: #fee2e2;
        color: #dc2626;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .event-meta {
            flex-direction: column;
            gap: 0.5rem;
        }

        .event-details {
            grid-template-columns: 1fr;
        }

        .event-header {
            padding: 1rem 1rem;
        }

        .event-content {
            padding: 0 1rem !important;
        }

        .event-item.expanded .event-content {
            padding: 0 1rem 1rem 1rem !important;
        }
    }
</style>
@endpush

@section('content')
<!-- Header Section -->
<div class="dashboard-header">
    <h4>📊 School Dashboard</h4>
    <p>Welcome to the School Management System - Real-time Overview</p>
</div>

<!-- Key Statistics Cards -->
<div class="stats-section">
    <div class="row g-3">
        <!-- Total Students Card -->
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-title text-muted mb-0">Total Students</h6>
                        <div class="icon icon-md bg-primary-light text-primary">
                            <i data-lucide="users" class="icon-lg"></i>
                        </div>
                    </div>
                    <div class="stat-number text-primary">{{ $totalStudents ?? 1250 }}</div>
                    <p class="text-success text-sm mb-0">
                        <i data-lucide="trending-up" class="icon-xs"></i>
                        <span>12% increase</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Teachers Card -->
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-title text-muted mb-0">Total Teachers</h6>
                        <div class="icon icon-md bg-success-light text-success">
                            <i data-lucide="award" class="icon-lg"></i>
                        </div>
                    </div>
                    <div class="stat-number text-success">{{ $totalTeachers ?? 85 }}</div>
                    <p class="text-success text-sm mb-0">
                        <i data-lucide="trending-up" class="icon-xs"></i>
                        <span>5% increase</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Active Classes Card -->
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-title text-muted mb-0">Active Classes</h6>
                        <div class="icon icon-md bg-warning-light text-warning">
                            <i data-lucide="book-open" class="icon-lg"></i>
                        </div>
                    </div>
                    <div class="stat-number text-warning">{{ $activeClasses ?? 42 }}</div>
                    <p class="text-warning text-sm mb-0">
                        <i data-lucide="alert-circle" class="icon-xs"></i>
                        <span>All running</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Attendance Today Card -->
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-title text-muted mb-0">Attendance Today</h6>
                        <div class="icon icon-md bg-info-light text-info">
                            <i data-lucide="check" class="icon-lg"></i>
                        </div>
                    </div>
                    <div class="stat-number text-info">{{ $attendancePercentage ?? '94' }}%</div>
                    <p class="text-success text-sm mb-0">
                        <i data-lucide="check-circle" class="icon-xs"></i>
                        <span>{{ $attendanceCount ?? 1175 }} present</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row g-3 mb-4">
    <!-- Attendance Trend Chart (Full Width) -->
    <div class="col-12">
        <div class="card chart-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">📈 Attendance Trend (Weekly)</h6>
                <small class="text-muted">Last 7 days performance</small>
            </div>
            <div class="card-body">
                <!-- Skeleton Loader -->
                <div id="attendanceTrendSkeleton" class="chart-body-loading">
                    <div class="skeleton-loader skeleton-chart"></div>
                </div>
                <!-- Actual Chart -->
                <div id="attendanceTrendChart" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-3 mb-4">
    <!-- Student Enrollment by Class Chart -->
    <div class="col-lg-6">
        <div class="card chart-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">👥 Student Enrollment by Class</h6>
            </div>
            <div class="card-body">
                <!-- Skeleton Loader -->
                <div id="enrollmentSkeleton" class="chart-body-loading">
                    <div class="skeleton-loader skeleton-chart"></div>
                </div>
                <!-- Actual Chart -->
                <div id="enrollmentChart" style="display: none;"></div>
            </div>
        </div>
    </div>

    <!-- Academic Performance Chart -->
    <div class="col-lg-6">
        <div class="card chart-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">📊 Academic Performance</h6>
            </div>
            <div class="card-body">
                <!-- Skeleton Loader -->
                <div id="performanceSkeleton" class="chart-body-loading">
                    <div class="skeleton-loader skeleton-chart"></div>
                </div>
                <!-- Actual Chart -->
                <div id="performanceChart" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Announcements and Events -->
<div class="row g-3">
    <!-- Recent Announcements -->
    <div class="col-lg-6">
        <div class="card announcement-event-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">📢 Recent Announcements</h6>
                <a href="#" class="text-primary text-sm">View All →</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($announcements ?? [] as $announcement)
                    <div class="list-group-item px-4 py-3 d-flex justify-content-between align-items-start border-start border-{{ array_keys(['primary' => 'primary', 'success' => 'success', 'info' => 'info', 'warning' => 'warning'])[$loop->index % 4] }} border-3">
                        <div style="flex: 1;">
                            <h6 class="mb-1 fw-600">{{ $announcement['title'] ?? 'Announcement' }}</h6>
                            <p class="text-muted text-sm mb-0">{{ $announcement['description'] ?? '' }}</p>
                        </div>
                        <span class="badge bg-{{ $announcement['type'] ?? 'info' }} ms-2">{{ ucfirst($announcement['type'] ?? 'info') }}</span>
                    </div>
                    @empty
                    <div class="list-group-item px-4 py-3 d-flex justify-content-between align-items-start border-start border-success border-3">
                        <div style="flex: 1;">
                            <h6 class="mb-1 fw-600">School Holiday Notice</h6>
                            <p class="text-muted text-sm mb-0">School will be closed on December 25-26</p>
                        </div>
                        <span class="badge bg-success ms-2">New</span>
                    </div>
                    <div class="list-group-item px-4 py-3 d-flex justify-content-between align-items-start border-start border-info border-3">
                        <div style="flex: 1;">
                            <h6 class="mb-1 fw-600">Final Exam Schedule Released</h6>
                            <p class="text-muted text-sm mb-0">Check the exam timetable for your class</p>
                        </div>
                        <span class="badge bg-info ms-2">Info</span>
                    </div>
                    <div class="list-group-item px-4 py-3 d-flex justify-content-between align-items-start border-start border-warning border-3">
                        <div style="flex: 1;">
                            <h6 class="mb-1 fw-600">Parent-Teacher Meeting</h6>
                            <p class="text-muted text-sm mb-0">Scheduled for December 15, 2025</p>
                        </div>
                        <span class="badge bg-warning ms-2">Important</span>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Events -->
    <div class="col-lg-6">
        <div class="card announcement-event-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">📅 Upcoming Events</h6>
                <a href="#" class="text-primary text-sm">View All →</a>
            </div>
            <div class="card-body p-0">
                @forelse($events ?? [] as $event)
                <ul class="event-accordion">
                    <li class="event-item" data-event-id="{{ $event['id'] ?? '' }}">
                        <!-- Event Header (Always Visible) -->
                        <div class="event-header">
                            <div class="event-header-left">
                                <div class="event-color-indicator" style="background-color: var(--bs-{{ $event['color'] ?? 'primary' }})"></div>
                                <div class="event-header-content">
                                    <h6 class="event-title">{{ $event['title'] ?? 'Event' }}</h6>
                                    <p class="event-meta">
                                        <span class="event-meta-item">
                                            <i data-lucide="calendar" style="width: 14px; height: 14px;"></i>
                                            {{ $event['date'] ?? '' }}
                                        </span>
                                        <span class="event-meta-item">
                                            <i data-lucide="map-pin" style="width: 14px; height: 14px;"></i>
                                            {{ $event['location'] ?? 'TBD' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <i class="event-toggle-icon" data-lucide="chevron-down" style="width: 20px; height: 20px;"></i>
                        </div>

                        <!-- Event Content (Collapsible) -->
                        <div class="event-content">
                            @if($event['description'])
                            <p class="event-description">{{ $event['description'] }}</p>
                            @endif

                            <div class="event-details">
                                <!-- Date & Time -->
                                <div class="event-detail-item">
                                    <div class="event-detail-icon">
                                        <i data-lucide="clock" style="width: 16px; height: 16px;"></i>
                                    </div>
                                    <div class="event-detail-text">
                                        <span class="event-detail-label">Time</span>
                                        <span class="event-detail-value">
                                            {{ $event['start_time'] ?? 'Not set' }}
                                            @if($event['end_time'] !== 'Not set')
                                            - {{ $event['end_time'] }}
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                <!-- Category -->
                                <div class="event-detail-item">
                                    <div class="event-detail-icon">
                                        <i data-lucide="tag" style="width: 16px; height: 16px;"></i>
                                    </div>
                                    <div class="event-detail-text">
                                        <span class="event-detail-label">Category</span>
                                        <span class="event-detail-value">{{ $event['category'] ?? 'General' }}</span>
                                    </div>
                                </div>

                                <!-- Location -->
                                <div class="event-detail-item">
                                    <div class="event-detail-icon">
                                        <i data-lucide="map-pin" style="width: 16px; height: 16px;"></i>
                                    </div>
                                    <div class="event-detail-text">
                                        <span class="event-detail-label">Location</span>
                                        <span class="event-detail-value">{{ $event['location'] ?? 'TBD' }}</span>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="event-detail-item">
                                    <div class="event-detail-icon">
                                        <i data-lucide="info" style="width: 16px; height: 16px;"></i>
                                    </div>
                                    <div class="event-detail-text">
                                        <span class="event-detail-label">Status</span>
                                        <span class="event-status-badge event-status-{{ $event['status'] ?? 'upcoming' }}">
                                            {{ ucfirst($event['status'] ?? 'upcoming') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if($event['notes'])
                            <div style="padding: 0.75rem; background-color: #f8f9fa; border-radius: 4px; border-left: 3px solid #0d6efd;">
                                <p style="margin: 0; color: #495057; font-size: 0.85rem;">
                                    <strong>Notes:</strong> {{ $event['notes'] }}
                                </p>
                            </div>
                            @endif
                        </div>
                    </li>
                </ul>
                @empty
                <div style="padding: 2rem; text-align: center; color: #999;">
                    <p style="margin: 0; font-size: 2rem;">📭</p>
                    <p style="margin: 0.5rem 0 0 0;">No upcoming events scheduled</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fetch chart data via AJAX
        fetch('{{ route("dashboard.chart-data") }}')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (typeof ApexCharts !== 'undefined') {
                    // Extract data from response
                    const enrollmentData = data.enrollmentData;
                    const enrollmentLabels = data.enrollmentLabels;
                    const performanceData = data.performanceData;
                    const performanceLabels = data.performanceLabels;
                    const attendanceTrendData = data.attendanceTrendData;
                    const attendanceTrendLabels = data.attendanceTrendLabels;

                    // Hide skeleton and show chart container FIRST
                    document.getElementById('enrollmentSkeleton').style.display = 'none';
                    document.getElementById('enrollmentChart').style.display = 'block';

                    // 1. Student Enrollment by Class - Bar Chart
                    const enrollmentOptions = {
                        chart: {
                            type: 'bar',
                            height: 350,
                            toolbar: {
                                show: true
                            }
                        },
                        series: [{
                            name: 'Students',
                            data: enrollmentData
                        }],
                        xaxis: {
                            categories: enrollmentLabels
                        },
                        colors: ['#0d6efd'],
                        grid: {
                            borderColor: '#e7e7e7',
                            strokeDashArray: 5
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: '60%',
                                borderRadius: 5
                            }
                        }
                    };
                    new ApexCharts(document.querySelector("#enrollmentChart"), enrollmentOptions).render();

                    // Hide skeleton and show chart container FIRST
                    document.getElementById('performanceSkeleton').style.display = 'none';
                    document.getElementById('performanceChart').style.display = 'block';

                    // 2. Academic Performance - Bar Chart
                    const performanceOptions = {
                        chart: {
                            type: 'bar',
                            height: 350,
                            toolbar: {
                                show: true
                            }
                        },
                        series: [{
                            name: 'Average Score',
                            data: performanceData
                        }],
                        xaxis: {
                            categories: performanceLabels
                        },
                        colors: ['#198754'],
                        grid: {
                            borderColor: '#e7e7e7',
                            strokeDashArray: 5
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: '60%',
                                borderRadius: 5
                            }
                        }
                    };
                    new ApexCharts(document.querySelector("#performanceChart"), performanceOptions).render();

                    // Hide skeleton and show chart container FIRST
                    document.getElementById('attendanceTrendSkeleton').style.display = 'none';
                    document.getElementById('attendanceTrendChart').style.display = 'block';

                    // 3. Attendance Trend - Area Chart
                    const attendanceTrendOptions = {
                        chart: {
                            type: 'area',
                            height: 350,
                            stacked: false,
                            toolbar: {
                                show: true
                            }
                        },
                        series: [{
                            name: 'Attendance %',
                            data: attendanceTrendData
                        }],
                        xaxis: {
                            categories: attendanceTrendLabels
                        },
                        colors: ['#198754'],
                        fill: {
                            type: 'gradient',
                            gradient: {
                                opacityFrom: 0.7,
                                opacityTo: 0.1
                            }
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 2
                        },
                        yaxis: {
                            min: 0,
                            max: 100
                        }
                    };
                    new ApexCharts(document.querySelector("#attendanceTrendChart"), attendanceTrendOptions).render();
                } else {
                    console.warn('ApexCharts library not loaded');
                }
            })
            .catch(error => {
                console.error('Error fetching chart data:', error);
                // Hide all skeletons on error
                document.getElementById('enrollmentSkeleton').style.display = 'none';
                document.getElementById('performanceSkeleton').style.display = 'none';
                document.getElementById('attendanceTrendSkeleton').style.display = 'none';
            });

        // Event Accordion Functionality
        initializeEventAccordion();
    });

    function initializeEventAccordion() {
        const eventItems = document.querySelectorAll('.event-item');

        eventItems.forEach(item => {
            const header = item.querySelector('.event-header');
            const content = item.querySelector('.event-content');

            if (header && content) {
                header.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleEventItem(item);
                });

                // Optional: Close other items when opening one (accordion behavior)
                // Uncomment the following lines if you want only one event open at a time
                // header.addEventListener('click', function() {
                //     eventItems.forEach(otherItem => {
                //         if (otherItem !== item && otherItem.classList.contains('expanded')) {
                //             toggleEventItem(otherItem);
                //         }
                //     });
                // });
            }
        });
    }

    function toggleEventItem(item) {
        item.classList.toggle('expanded');

        // Optional: Smooth scroll to expanded item
        if (item.classList.contains('expanded')) {
            setTimeout(() => {
                item.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }, 150);
        }
    }
</script>
@endpush