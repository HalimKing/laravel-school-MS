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

    .dashboard-grid {
        display: grid;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .metrics-grid {
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }

    .analytics-grid {
        grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
    }

    .content-grid {
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    }

    .section-empty-state {
        border: 1px dashed #d8dbe0;
        border-radius: 12px;
        background: #f8fafc;
        padding: 1.5rem;
        color: #6c757d;
        text-align: center;
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

@if($canViewMetrics)
<div class="stats-section dashboard-grid metrics-grid">
    <div class="card stat-card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="card-title text-muted mb-0">Total Students</h6>
                <div class="icon icon-md bg-primary-light text-primary">
                    <i data-lucide="users" class="icon-lg"></i>
                </div>
            </div>
            <div class="stat-number text-primary">{{ $totalStudents ?? 0 }}</div>
            <p class="text-success text-sm mb-0">
                <i data-lucide="trending-up" class="icon-xs"></i>
                <span>Live system count</span>
            </p>
        </div>
    </div>

    <div class="card stat-card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="card-title text-muted mb-0">Total Teachers</h6>
                <div class="icon icon-md bg-success-light text-success">
                    <i data-lucide="award" class="icon-lg"></i>
                </div>
            </div>
            <div class="stat-number text-success">{{ $totalTeachers ?? 0 }}</div>
            <p class="text-success text-sm mb-0">
                <i data-lucide="trending-up" class="icon-xs"></i>
                <span>Live system count</span>
            </p>
        </div>
    </div>

    <div class="card stat-card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="card-title text-muted mb-0">Active Classes</h6>
                <div class="icon icon-md bg-warning-light text-warning">
                    <i data-lucide="book-open" class="icon-lg"></i>
                </div>
            </div>
            <div class="stat-number text-warning">{{ $activeClasses ?? 0 }}</div>
            <p class="text-warning text-sm mb-0">
                <i data-lucide="alert-circle" class="icon-xs"></i>
                <span>All running</span>
            </p>
        </div>
    </div>

    <div class="card stat-card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="card-title text-muted mb-0">Attendance Today</h6>
                <div class="icon icon-md bg-info-light text-info">
                    <i data-lucide="check" class="icon-lg"></i>
                </div>
            </div>
            <div class="stat-number text-info">{{ $attendancePercentage ?? 0 }}%</div>
            <p class="text-success text-sm mb-0">
                <i data-lucide="check-circle" class="icon-xs"></i>
                <span>{{ $attendanceCount ?? 0 }} present</span>
            </p>
        </div>
    </div>
</div>
@endif

@if($canViewAttendanceTrend || $canViewEnrollment || $canViewPerformance)
<div class="dashboard-grid analytics-grid mb-4">
    @if($canViewAttendanceTrend)
    <div class="card chart-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="card-title mb-0">📈 Attendance Trend (Weekly)</h6>
            <small class="text-muted">Last 7 days performance</small>
        </div>
        <div class="card-body">
            <div id="attendanceTrendSkeleton" class="chart-body-loading">
                <div class="skeleton-loader skeleton-chart"></div>
            </div>
            <div id="attendanceTrendChart" style="display: none;"></div>
        </div>
    </div>
    @endif

    @if($canViewEnrollment)
    <div class="card chart-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="card-title mb-0">👥 Student Enrollment by Class</h6>
        </div>
        <div class="card-body">
            <div id="enrollmentSkeleton" class="chart-body-loading">
                <div class="skeleton-loader skeleton-chart"></div>
            </div>
            <div id="enrollmentChart" style="display: none;"></div>
        </div>
    </div>
    @endif

    @if($canViewPerformance)
    <div class="card chart-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="card-title mb-0">📊 Academic Performance</h6>
        </div>
        <div class="card-body">
            <div id="performanceSkeleton" class="chart-body-loading">
                <div class="skeleton-loader skeleton-chart"></div>
            </div>
            <div id="performanceChart" style="display: none;"></div>
        </div>
    </div>
    @endif
</div>
@endif

@if($canViewAnnouncements || $canViewEvents)
<div class="dashboard-grid content-grid">
    @if($canViewAnnouncements)
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
                <div class="section-empty-state">No announcements available for your role.</div>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    @if($canViewEvents)
    <div class="card announcement-event-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="card-title mb-0">📅 Upcoming Events</h6>
            <a href="#" class="text-primary text-sm">View All →</a>
        </div>
        <div class="card-body p-0">
            @forelse($events ?? [] as $event)
            <ul class="event-accordion">
                <li class="event-item" data-event-id="{{ $event['id'] ?? '' }}">
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

                    <div class="event-content">
                        @if($event['description'])
                        <p class="event-description">{{ $event['description'] }}</p>
                        @endif

                        <div class="event-details">
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

                            <div class="event-detail-item">
                                <div class="event-detail-icon">
                                    <i data-lucide="tag" style="width: 16px; height: 16px;"></i>
                                </div>
                                <div class="event-detail-text">
                                    <span class="event-detail-label">Category</span>
                                    <span class="event-detail-value">{{ $event['category'] ?? 'General' }}</span>
                                </div>
                            </div>

                            <div class="event-detail-item">
                                <div class="event-detail-icon">
                                    <i data-lucide="map-pin" style="width: 16px; height: 16px;"></i>
                                </div>
                                <div class="event-detail-text">
                                    <span class="event-detail-label">Location</span>
                                    <span class="event-detail-value">{{ $event['location'] ?? 'TBD' }}</span>
                                </div>
                            </div>

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
            <div class="section-empty-state">No upcoming events scheduled.</div>
            @endforelse
        </div>
    </div>
    @endif
</div>
@endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dashboardPermissions = {
            canViewAttendanceTrend: @json((bool) $canViewAttendanceTrend),
            canViewEnrollment: @json((bool) $canViewEnrollment),
            canViewPerformance: @json((bool) $canViewPerformance),
        };

        const hasCharts = dashboardPermissions.canViewAttendanceTrend || dashboardPermissions.canViewEnrollment || dashboardPermissions.canViewPerformance;

        if (hasCharts) {
            fetch('{{ route("dashboard.chart-data") }}')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (typeof ApexCharts === 'undefined') {
                        console.warn('ApexCharts library not loaded');
                        return;
                    }

                    const renderBarChart = (containerId, skeletonId, labels, seriesData, seriesName, color) => {
                        const container = document.getElementById(containerId);
                        const skeleton = document.getElementById(skeletonId);

                        if (!container || !skeleton) {
                            return;
                        }

                        skeleton.style.display = 'none';
                        container.style.display = 'block';

                        const options = {
                            chart: {
                                type: 'bar',
                                height: 350,
                                toolbar: {
                                    show: true
                                }
                            },
                            series: [{
                                name: seriesName,
                                data: seriesData
                            }],
                            xaxis: {
                                categories: labels
                            },
                            colors: [color],
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

                        new ApexCharts(container, options).render();
                    };

                    const renderAreaChart = (containerId, skeletonId, labels, seriesData, seriesName, color) => {
                        const container = document.getElementById(containerId);
                        const skeleton = document.getElementById(skeletonId);

                        if (!container || !skeleton) {
                            return;
                        }

                        skeleton.style.display = 'none';
                        container.style.display = 'block';

                        const options = {
                            chart: {
                                type: 'area',
                                height: 350,
                                stacked: false,
                                toolbar: {
                                    show: true
                                }
                            },
                            series: [{
                                name: seriesName,
                                data: seriesData
                            }],
                            xaxis: {
                                categories: labels
                            },
                            colors: [color],
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

                        new ApexCharts(container, options).render();
                    };

                    if (dashboardPermissions.canViewEnrollment && data.enrollment) {
                        renderBarChart('enrollmentChart', 'enrollmentSkeleton', data.enrollment.labels, data.enrollment.data, 'Students', '#0d6efd');
                    }

                    if (dashboardPermissions.canViewPerformance && data.performance) {
                        renderBarChart('performanceChart', 'performanceSkeleton', data.performance.labels, data.performance.data, 'Average Score', '#198754');
                    }

                    if (dashboardPermissions.canViewAttendanceTrend && data.attendanceTrend) {
                        renderAreaChart('attendanceTrendChart', 'attendanceTrendSkeleton', data.attendanceTrend.labels, data.attendanceTrend.data, 'Attendance %', '#198754');
                    }
                })
                .catch(error => {
                    console.error('Error fetching chart data:', error);

                    ['enrollmentSkeleton', 'performanceSkeleton', 'attendanceTrendSkeleton'].forEach(function(id) {
                        const node = document.getElementById(id);
                        if (node) {
                            node.style.display = 'none';
                        }
                    });
                });
        }

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