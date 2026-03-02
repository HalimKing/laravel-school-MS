@extends('layouts.app')

@section('title', 'Attendance Analytics')

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">Attendance Analytics Dashboard</h6>
        <div>
            <a href="{{ route('admin.attendance.create') }}" class="btn btn-success btn-sm">
                <i data-lucide="plus" style="width: 14px;" class="me-1"></i>Take Attendance
            </a>
        </div>
    </div>
</div>

@include('includes.message')

<!-- Date Range Filters -->
<div class="card mb-4">
    <div class="card-body">
        <h6 class="card-title fw-bold mb-3">Filter by Date Range</h6>
        <div class="btn-group" role="group">
            <a href="{{ route('admin.attendance.analytics', ['filter' => 'today']) }}"
                class="btn btn-sm {{ $filter === 'today' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i data-lucide="calendar" style="width: 14px;" class="me-1"></i>Today
            </a>
            <a href="{{ route('admin.attendance.analytics', ['filter' => '7d']) }}"
                class="btn btn-sm {{ $filter === '7d' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i data-lucide="calendar" style="width: 14px;" class="me-1"></i>Last 7 Days
            </a>
            <a href="{{ route('admin.attendance.analytics', ['filter' => '30d']) }}"
                class="btn btn-sm {{ $filter === '30d' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i data-lucide="calendar" style="width: 14px;" class="me-1"></i>Last 30 Days
            </a>
            <a href="{{ route('admin.attendance.analytics', ['filter' => '90d']) }}"
                class="btn btn-sm {{ $filter === '90d' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i data-lucide="calendar" style="width: 14px;" class="me-1"></i>Last 90 Days
            </a>
        </div>
    </div>
</div>
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-uppercase fw-bold small mb-2">Total Records</h6>
                <h3 class="mb-0 text-primary">{{ $totalAttendanceRecords }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-uppercase fw-bold small mb-2">Overall Attendance Rate</h6>
                <h3 class="mb-0 text-success">{{ $overallPercentage }}%</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-uppercase fw-bold small mb-2">Present / Absent / Late</h6>
                <h3 class="mb-0">{{ $presentCount }} / {{ $absentCount }} / {{ $lateCount }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Status Distribution Chart -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="card-title fw-bold mb-0">Status Distribution</h6>
            </div>
            <div class="card-body">
                <canvas id="statusChart" height="80"></canvas>
            </div>
        </div>
    </div>

    <!-- Daily Attendance Trend -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="card-title fw-bold mb-0">Daily Attendance Trend (Last 30 Days)</h6>
            </div>
            <div class="card-body">
                <canvas id="trendChart" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Class-wise Statistics -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h6 class="card-title fw-bold mb-0">Class-wise Attendance Rate</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Class</th>
                        <th class="text-end">Total Records</th>
                        <th class="text-end">Present</th>
                        <th class="text-end">Rate %</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classStats as $stat)
                    <tr>
                        <td>
                            <strong>{{ $stat['class'] }}</strong>
                        </td>
                        <td class="text-end">{{ $stat['total'] }}</td>
                        <td class="text-end text-success fw-bold">{{ $stat['present'] }}</td>
                        <td class="text-end">
                            <span class="badge bg-{{ $stat['percentage'] >= 80 ? 'success' : ($stat['percentage'] >= 70 ? 'warning' : 'danger') }}">
                                {{ $stat['percentage'] }}%
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">No class data available</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Subject-wise Statistics -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h6 class="card-title fw-bold mb-0">Subject-wise Attendance Rate</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Subject</th>
                        <th class="text-end">Total Records</th>
                        <th class="text-end">Present</th>
                        <th class="text-end">Rate %</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjectStats as $stat)
                    <tr>
                        <td>
                            <strong>{{ $stat['subject'] }}</strong>
                        </td>
                        <td class="text-end">{{ $stat['total'] }}</td>
                        <td class="text-end text-success fw-bold">{{ $stat['present'] }}</td>
                        <td class="text-end">
                            <span class="badge bg-{{ $stat['percentage'] >= 80 ? 'success' : ($stat['percentage'] >= 70 ? 'warning' : 'danger') }}">
                                {{ $stat['percentage'] }}%
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">No subject data available</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Top 10 Most Absent Students -->
<div class="card">
    <div class="card-header bg-light">
        <h6 class="card-title fw-bold mb-0">Top 10 Most Absent Students</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>ID</th>
                        <th>Class</th>
                        <th class="text-end">Total Attendance</th>
                        <th class="text-end">Absences</th>
                        <th class="text-end">Absence Rate %</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mostAbsentStudents as $student)
                    <tr>
                        <td>
                            <strong>{{ $student['name'] }}</strong>
                        </td>
                        <td>{{ $student['student_id'] }}</td>
                        <td>{{ $student['class'] }}</td>
                        <td class="text-end">{{ $student['total_attendance'] }}</td>
                        <td class="text-end text-danger fw-bold">{{ $student['absences'] }}</td>
                        <td class="text-end">
                            <span class="badge bg-{{ $student['absence_percentage'] > 30 ? 'danger' : ($student['absence_percentage'] > 15 ? 'warning' : 'success') }}">
                                {{ $student['absence_percentage'] }}%
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No student data available</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get filter label
        const filterLabels = {
            'today': 'Today',
            '7d': 'Last 7 Days',
            '30d': 'Last 30 Days',
            '90d': 'Last 90 Days'
        };
        const currentFilter = '{{ $filter }}';
        const filterLabel = filterLabels[currentFilter] || 'Last 30 Days';

        // Status Distribution Chart
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Present', 'Absent', 'Late', 'Excused'],
                    datasets: [{
                        data: [{
                                {
                                    $presentCount
                                }
                            },
                            {
                                {
                                    $absentCount
                                }
                            },
                            {
                                {
                                    $lateCount
                                }
                            },
                            {
                                {
                                    $excusedCount
                                }
                            }
                        ],
                        backgroundColor: [
                            '#28a745',
                            '#dc3545',
                            '#ffc107',
                            '#17a2b8'
                        ],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 12
                                },
                                padding: 15
                            }
                        }
                    }
                }
            });
        }

        // Daily Trend Chart
        const trendCtx = document.getElementById('trendChart');
        if (trendCtx) {
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: [
                        @foreach($dailyTrend as $trend)
                        '{{ $trend['
                        date '] }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Attendance Rate % (' + filterLabel + ')',
                        data: [
                            @foreach($dailyTrend as $trend) {
                                {
                                    $trend['percentage']
                                }
                            },
                            @endforeach
                        ],
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#007bff',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            labels: {
                                font: {
                                    size: 12
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            min: 0,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Reinitialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush

@endsection