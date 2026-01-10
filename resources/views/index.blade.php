@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    <style>
        .chart-container {
            position: relative;
            height: 300px;
        }
    </style>
@endpush

@section('content')
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-4">
        <div>
            <h4 class="mb-3 mb-md-0">School Dashboard</h4>
            <p class="text-muted mb-0">Welcome to the School Management System</p>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            <div class="input-group flatpickr w-200px me-2 mb-2 mb-md-0" id="dashboardDate">
                <span class="input-group-text input-group-addon bg-transparent border-primary" data-toggle>
                    <i data-lucide="calendar" class="text-primary"></i>
                </span>
                <input type="text" class="form-control bg-transparent border-primary" placeholder="Select date" data-input>
            </div>
            
        </div>
    </div>

    <!-- Key Statistics Cards -->
    <div class="row mb-4">
        <!-- Total Students Card -->
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title mb-2">Total Students</h6>
                            <h3 class="mb-0">{{ $totalStudents ?? 1250 }}</h3>
                            <p class="text-success text-sm mb-0 mt-1">
                                <i data-lucide="trending-up" class="icon-sm"></i>
                                <span>12% increase</span>
                            </p>
                        </div>
                        <div class="icon icon-md bg-primary-light text-primary">
                            <i data-lucide="users" class="icon-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Teachers Card -->
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title mb-2">Total Teachers</h6>
                            <h3 class="mb-0">{{ $totalTeachers ?? 85 }}</h3>
                            <p class="text-success text-sm mb-0 mt-1">
                                <i data-lucide="trending-up" class="icon-sm"></i>
                                <span>5% increase</span>
                            </p>
                        </div>
                        <div class="icon icon-md bg-success-light text-success">
                            <i data-lucide="award" class="icon-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Classes Card -->
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title mb-2">Active Classes</h6>
                            <h3 class="mb-0">{{ $activeClasses ?? 42 }}</h3>
                            <p class="text-warning text-sm mb-0 mt-1">
                                <i data-lucide="alert-circle" class="icon-sm"></i>
                                <span>All running</span>
                            </p>
                        </div>
                        <div class="icon icon-md bg-warning-light text-warning">
                            <i data-lucide="book-open" class="icon-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Today Card -->
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title mb-2">Attendance Today</h6>
                            <h3 class="mb-0">{{ $attendancePercentage ?? '94%' }}</h3>
                            <p class="text-success text-sm mb-0 mt-1">
                                <i data-lucide="check-circle" class="icon-sm"></i>
                                <span>{{ $attendanceCount ?? 1175 }} present</span>
                            </p>
                        </div>
                        <div class="icon icon-md bg-info-light text-info">
                            <i data-lucide="check" class="icon-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <!-- Student Enrollment by Class Chart -->
        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title">Student Enrollment by Class</h6>
                </div>
                <div class="card-body">
                    <div id="enrollmentChart"></div>
                </div>
            </div>
        </div>

        <!-- Academic Performance Chart -->
        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title">Academic Performance</h6>
                </div>
                <div class="card-body">
                    <div id="performanceChart"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Charts Row -->
    <div class="row mb-4">
        <!-- Attendance Trend Chart -->
        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title">Attendance Trend (Weekly)</h6>
                </div>
                <div class="card-body">
                    <div id="attendanceTrendChart"></div>
                </div>
            </div>
        </div>

        <!-- Grade Distribution Chart -->
        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title">Grade Distribution</h6>
                </div>
                <div class="card-body">
                    <div id="gradeDistributionChart"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Announcements and Events -->
    <div class="row">
        <!-- Recent Announcements -->
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title">Recent Announcements</h6>
                    <a href="#" class="text-primary text-sm">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($announcements ?? [] as $announcement)
                            <div class="list-group-item px-4 py-3 d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $announcement['title'] ?? 'Announcement' }}</h6>
                                    <p class="text-secondary text-sm mb-0">{{ $announcement['description'] ?? '' }}</p>
                                </div>
                                <span class="badge bg-{{ $announcement['type'] ?? 'info' }}">{{ ucfirst($announcement['type'] ?? 'info') }}</span>
                            </div>
                        @empty
                            <div class="list-group-item px-4 py-3 d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">School Holiday Notice</h6>
                                    <p class="text-secondary text-sm mb-0">School will be closed on December 25-26</p>
                                </div>
                                <span class="badge bg-success">New</span>
                            </div>
                            <div class="list-group-item px-4 py-3 d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">Final Exam Schedule Released</h6>
                                    <p class="text-secondary text-sm mb-0">Check the exam timetable for your class</p>
                                </div>
                                <span class="badge bg-info">Info</span>
                            </div>
                            <div class="list-group-item px-4 py-3 d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">Parent-Teacher Meeting</h6>
                                    <p class="text-secondary text-sm mb-0">Scheduled for December 15, 2025</p>
                                </div>
                                <span class="badge bg-warning">Important</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title">Upcoming Events</h6>
                    <a href="#" class="text-primary text-sm">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($events ?? [] as $event)
                            <div class="list-group-item px-4 py-3 border-start border-{{ $event['color'] ?? 'primary' }} border-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $event['title'] ?? 'Event' }}</h6>
                                        <p class="text-secondary text-sm mb-0">{{ $event['date'] ?? '' }}</p>
                                    </div>
                                    <i data-lucide="calendar" class="text-{{ $event['color'] ?? 'primary' }}"></i>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item px-4 py-3 border-start border-success border-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">Annual Sports Day</h6>
                                        <p class="text-secondary text-sm mb-0">December 20, 2025</p>
                                    </div>
                                    <i data-lucide="calendar" class="text-success"></i>
                                </div>
                            </div>
                            <div class="list-group-item px-4 py-3 border-start border-primary border-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">Science Exhibition</h6>
                                        <p class="text-secondary text-sm mb-0">December 28, 2025</p>
                                    </div>
                                    <i data-lucide="calendar" class="text-primary"></i>
                                </div>
                            </div>
                            <div class="list-group-item px-4 py-3 border-start border-warning border-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">New Year Celebration</h6>
                                        <p class="text-secondary text-sm mb-0">January 1, 2026</p>
                                    </div>
                                    <i data-lucide="calendar" class="text-warning"></i>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof ApexCharts !== 'undefined') {
                // 1. Student Enrollment by Class - Bar Chart
                const enrollmentOptions = {
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: { show: true }
                    },
                    series: [{
                        name: 'Students',
                        data: [120, 135, 142, 128, 138, 145, 150, 118]
                    }],
                    xaxis: {
                        categories: ['Class 6A', 'Class 6B', 'Class 7A', 'Class 7B', 'Class 8A', 'Class 8B', 'Class 9A', 'Class 9B']
                    },
                    colors: ['#0d6efd'],
                    grid: { borderColor: '#e7e7e7', strokeDashArray: 5 },
                    plotOptions: {
                        bar: { columnWidth: '60%', borderRadius: 5 }
                    }
                };
                new ApexCharts(document.querySelector("#enrollmentChart"), enrollmentOptions).render();

                // 2. Academic Performance - Multi-line Chart
                const performanceOptions = {
                    chart: {
                        type: 'line',
                        height: 350,
                        toolbar: { show: true },
                        zoom: { enabled: true }
                    },
                    series: [
                        { name: 'Excellent (A)', data: [45, 52, 48, 65, 59, 68, 72] },
                        { name: 'Good (B)', data: [38, 42, 45, 52, 58, 62, 65] },
                        { name: 'Average (C)', data: [25, 28, 32, 35, 40, 38, 42] },
                        { name: 'Below Average (D)', data: [12, 15, 10, 8, 5, 8, 6] }
                    ],
                    xaxis: {
                        categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7']
                    },
                    colors: ['#198754', '#0dcaf0', '#ffc107', '#dc3545'],
                    stroke: { curve: 'smooth', width: 2 },
                    markers: { size: 4, colors: ['#198754', '#0dcaf0', '#ffc107', '#dc3545'] }
                };
                new ApexCharts(document.querySelector("#performanceChart"), performanceOptions).render();

                // 3. Attendance Trend - Area Chart
                const attendanceTrendOptions = {
                    chart: {
                        type: 'area',
                        height: 350,
                        stacked: false,
                        toolbar: { show: true }
                    },
                    series: [
                        { name: 'Present', data: [1150, 1160, 1175, 1185, 1190, 1200, 1175] },
                        { name: 'Absent', data: [75, 65, 50, 40, 35, 25, 50] }
                    ],
                    xaxis: {
                        categories: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']
                    },
                    colors: ['#198754', '#dc3545'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            opacityFrom: 0.7,
                            opacityTo: 0.1
                        }
                    },
                    stroke: { curve: 'smooth', width: 2 }
                };
                new ApexCharts(document.querySelector("#attendanceTrendChart"), attendanceTrendOptions).render();

                // 4. Grade Distribution - Pie Chart
                const gradeDistributionOptions = {
                    chart: {
                        type: 'pie',
                        height: 350
                    },
                    series: [320, 245, 180, 95],
                    labels: ['Excellent (A)', 'Good (B)', 'Average (C)', 'Below Average (D)'],
                    colors: ['#198754', '#0dcaf0', '#ffc107', '#dc3545'],
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%',
                                labels: {
                                    show: true,
                                    name: { show: true },
                                    value: { show: true }
                                }
                            }
                        }
                    },
                    legend: { position: 'bottom' }
                };
                new ApexCharts(document.querySelector("#gradeDistributionChart"), gradeDistributionOptions).render();
            }
        });
    </script>
@endpush