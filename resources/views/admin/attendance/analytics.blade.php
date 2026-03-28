@extends('layouts.app')

@section('title', 'Attendance Analytics')

@push('styles')
<style>
    .analytics-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2.5rem 2rem;
        border-radius: 10px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .analytics-header h2 {
        font-weight: 700;
        margin-bottom: 0.5rem;
        font-size: 1.75rem;
    }

    .analytics-header p {
        opacity: 0.95;
        margin: 0;
        font-size: 0.95rem;
    }

    .filter-section {
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 2rem;
        border: 1px solid #e9ecef;
    }

    .filter-section h6 {
        font-weight: 600;
        margin-bottom: 1.25rem;
        color: #212529;
    }

    .filter-group {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        align-items: flex-end;
    }

    .filter-item {
        flex: 1;
        min-width: 200px;
    }

    .filter-item label {
        display: block;
        font-weight: 500;
        font-size: 0.85rem;
        margin-bottom: 0.5rem;
        color: #495057;
    }

    .filter-item select,
    .filter-item input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #e9ecef;
        border-radius: 4px;
        font-size: 0.9rem;
    }

    .filter-item input:focus,
    .filter-item select:focus {
        border-color: #667eea;
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .filter-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .filter-btn {
        padding: 0.5rem 0.75rem;
        border: 1px solid #e9ecef;
        background: white;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 500;
        font-size: 0.85rem;
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

    .stat-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1.5rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .stat-card.skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
    }

    .stat-label {
        display: block;
        font-size: 0.8rem;
        text-transform: uppercase;
        color: #6c757d;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .stat-meta {
        font-size: 0.85rem;
        color: #999;
    }

    .chart-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .chart-card h6 {
        font-weight: 600;
        margin-bottom: 1rem;
        color: #212529;
    }

    .chart-container {
        position: relative;
        height: 350px;
    }

    .table-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .table-card-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e9ecef;
        background: #f8f9fa;
    }

    .table-card-header h6 {
        font-weight: 600;
        margin: 0;
        color: #212529;
    }

    .btn-outline-primary:hover,
    .btn-outline-success:hover {
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        background: #667eea;
        border-color: #667eea;
        color: white;
    }

    .btn-outline-success:hover {
        background: #28a745;
        border-color: #28a745;
        color: white;
    }

    .table-card table {
        margin-bottom: 0;
    }

    .table-card tbody tr:hover {
        background-color: #f8f9fa;
    }

    .status-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
        white-space: nowrap;
    }

    .status-excellent {
        background: #d1fae5;
        color: #065f46;
    }

    .status-good {
        background: #dbeafe;
        color: #0c2d6b;
    }

    .status-fair {
        background: #fef3c7;
        color: #92400e;
    }

    .status-poor {
        background: #fee2e2;
        color: #7f1d1d;
    }

    .loading-skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 2s infinite;
        border-radius: 4px;
        height: 1rem;
        margin-bottom: 0.5rem;
    }

    @media (max-width: 768px) {
        .filter-group {
            flex-direction: column;
        }

        .filter-item {
            min-width: 100%;
        }

        .filter-buttons {
            width: 100%;
        }

        .filter-btn {
            flex: 1;
        }

        .analytics-header {
            padding: 2rem 1rem;
        }

        .analytics-header h2 {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@section('content')

<!-- Analytics Header -->
<div class="analytics-header">
    <h2>📊 Attendance Analytics</h2>
    <p>Comprehensive attendance tracking and insights</p>
</div>

@include('includes.message')

<!-- Filter Section -->
<div class="filter-section">
    <h6><i data-lucide="filter" style="width: 18px; height: 18px; margin-right: 0.5rem;"></i>Filter Analytics</h6>

    <div class="filter-group">
        <div class="filter-item">
            <label for="filterClass">Class</label>
            <select id="filterClass" class="filter-input">
                <option value="">All Classes</option>
                @foreach($classes as $class)
                <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-item">
            <label for="filterSubject">Subject</label>
            <select id="filterSubject" class="filter-input">
                <option value="">All Subjects</option>
                @foreach($subjects as $subject)
                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-item">
            <label for="filterDateFrom">From Date</label>
            <input type="date" id="filterDateFrom" class="filter-input" value="{{ now()->subDays(30)->format('Y-m-d') }}">
        </div>

        <div class="filter-item">
            <label for="filterDateTo">To Date</label>
            <input type="date" id="filterDateTo" class="filter-input" value="{{ now()->format('Y-m-d') }}">
        </div>

        <div class="filter-item">
            <div class="filter-buttons">
                <button class="filter-btn active" data-range="7d">Last 7 Days</button>
                <button class="filter-btn" data-range="30d">Last 30 Days</button>
                <button class="filter-btn" data-range="90d">Last 90 Days</button>
            </div>
        </div>

        <div class="filter-item" style="min-width: unset;">
            <button id="applyFilters" class="btn btn-primary btn-sm" style="padding: 0.5rem 1rem;">
                <i data-lucide="search" style="width: 14px; height: 14px; margin-right: 0.35rem;"></i>Apply Filters
            </button>
        </div>
    </div>
</div>

<div id="analyticsLoading" class="alert alert-info d-none" role="alert">
    Loading analytics data...
</div>

<div id="analyticsError" class="alert alert-danger d-none" role="alert"></div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4" id="statsContainer">
    <!-- Total Records Card -->
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <span class="stat-label">Total Records</span>
            <div class="stat-value text-primary" id="statTotal">0</div>
            <span class="stat-meta">Attendance entries</span>
        </div>
    </div>

    <!-- Attendance Rate Card -->
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <span class="stat-label">Attendance Rate</span>
            <div class="stat-value text-success" id="statRate">0%</div>
            <span class="stat-meta">Overall presence</span>
        </div>
    </div>

    <!-- Present Card -->
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <span class="stat-label">Present</span>
            <div class="stat-value" style="color: #28a745;" id="statPresent">0</div>
            <span class="stat-meta">Students marked present</span>
        </div>
    </div>

    <!-- Absent Card -->
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <span class="stat-label">Absent</span>
            <div class="stat-value" style="color: #dc3545;" id="statAbsent">0</div>
            <span class="stat-meta">Students absent</span>
        </div>
    </div>
</div>

<!-- Additional Stats Row -->
<div class="row g-3 mb-4">
    <!-- Late Card -->
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <span class="stat-label">Late</span>
            <div class="stat-value" style="color: #ffc107;" id="statLate">0</div>
            <span class="stat-meta">Marked late</span>
        </div>
    </div>

    <!-- Excused Card -->
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <span class="stat-label">Excused</span>
            <div class="stat-value" style="color: #17a2b8;" id="statExcused">0</div>
            <span class="stat-meta">with excuse</span>
        </div>
    </div>

    <!-- Active Classes Card -->
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <span class="stat-label">Classes</span>
            <div class="stat-value" style="color: #6f42c1;" id="statClasses">0</div>
            <span class="stat-meta">With records</span>
        </div>
    </div>

    <!-- Active Subjects Card -->
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <span class="stat-label">Subjects</span>
            <div class="stat-value" style="color: #fd7e14;" id="statSubjects">0</div>
            <span class="stat-meta">With records</span>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row g-3 mb-4">
    <!-- Status Distribution Chart -->
    <div class="col-lg-6">
        <div class="chart-card">
            <h6>Attendance Status Distribution</h6>
            <div class="chart-container">
                <div id="statusChart"></div>
            </div>
        </div>
    </div>

    <!-- Daily Trend Chart -->
    <div class="col-lg-6">
        <div class="chart-card">
            <h6>Daily Attendance Trend</h6>
            <div class="chart-container">
                <div id="trendChart"></div>
            </div>
        </div>
    </div>
</div>

<!-- Class-wise Attendance Chart -->
<div class="chart-card">
    <h6>Class-wise Attendance Performance</h6>
    <div class="chart-container" style="height: 400px;">
        <div id="classChart"></div>
    </div>
</div>

<!-- Subject-wise Attendance Chart -->
<div class="chart-card">
    <h6>Subject-wise Attendance Performance</h6>
    <div class="chart-container" style="height: 400px;">
        <div id="subjectChart"></div>
    </div>
</div>

<!-- Class Statistics Table -->
<div class="table-card">
    <div class="table-card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h6 style="margin: 0;">Class-wise Attendance Statistics</h6>
        <div style="display: flex; gap: 0.5rem;">
            <button class="btn btn-sm btn-outline-primary" onclick="downloadTableAsCSV('classStatsTable', 'class-attendance.csv')">
                <i data-lucide="download" style="width: 14px; height: 14px; margin-right: 0.35rem;"></i>CSV
            </button>
            <button class="btn btn-sm btn-outline-success" onclick="downloadTableAsExcel('classStatsTable', 'class-attendance.xlsx')">
                <i data-lucide="download" style="width: 14px; height: 14px; margin-right: 0.35rem;"></i>Excel
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-sm">
            <thead class="table-light">
                <tr>
                    <th>Class</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">Present</th>
                    <th class="text-end">Absent</th>
                    <th class="text-end">Late</th>
                    <th class="text-end">Rate</th>
                </tr>
            </thead>
            <tbody id="classStatsTable">
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No class data available</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Subject Statistics Table -->
<div class="table-card">
    <div class="table-card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h6 style="margin: 0;">Subject-wise Attendance Statistics</h6>
        <div style="display: flex; gap: 0.5rem;">
            <button class="btn btn-sm btn-outline-primary" onclick="downloadTableAsCSV('subjectStatsTable', 'subject-attendance.csv')">
                <i data-lucide="download" style="width: 14px; height: 14px; margin-right: 0.35rem;"></i>CSV
            </button>
            <button class="btn btn-sm btn-outline-success" onclick="downloadTableAsExcel('subjectStatsTable', 'subject-attendance.xlsx')">
                <i data-lucide="download" style="width: 14px; height: 14px; margin-right: 0.35rem;"></i>Excel
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-sm">
            <thead class="table-light">
                <tr>
                    <th>Subject</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">Present</th>
                    <th class="text-end">Absent</th>
                    <th class="text-end">Late</th>
                    <th class="text-end">Rate</th>
                </tr>
            </thead>
            <tbody id="subjectStatsTable">
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No subject data available</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Most Absent Students Table -->
<div class="table-card">
    <div class="table-card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h6 style="margin: 0;">Students with High Absence Rate</h6>
        <div style="display: flex; gap: 0.5rem;">
            <button class="btn btn-sm btn-outline-primary" onclick="downloadTableAsCSV('absentStudentsTable', 'absent-students.csv')">
                <i data-lucide="download" style="width: 14px; height: 14px; margin-right: 0.35rem;"></i>CSV
            </button>
            <button class="btn btn-sm btn-outline-success" onclick="downloadTableAsExcel('absentStudentsTable', 'absent-students.xlsx')">
                <i data-lucide="download" style="width: 14px; height: 14px; margin-right: 0.35rem;"></i>Excel
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-sm">
            <thead class="table-light">
                <tr>
                    <th>Student Name</th>
                    <th>ID</th>
                    <th>Class</th>
                    <th class="text-end">Total Attendance</th>
                    <th class="text-end">Absences</th>
                    <th class="text-end">Absence Rate</th>
                </tr>
            </thead>
            <tbody id="absentStudentsTable">
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No student data available</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    const endpoints = {
        data: "{{ route('admin.attendance.analytics.data') }}"
    };

    const chartInstances = {
        status: null,
        trend: null,
        classWise: null,
        subjectWise: null,
    };

    let currentAnalyticsData = {
        classStats: [],
        subjectStats: [],
        mostAbsentStudents: []
    };

    document.addEventListener('DOMContentLoaded', function() {
        initializeFilters();
        fetchAndRenderAnalytics();

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    function initializeFilters() {
        const filterBtns = document.querySelectorAll('.filter-btn');
        const applyBtn = document.getElementById('applyFilters');
        const filterClass = document.getElementById('filterClass');
        const filterSubject = document.getElementById('filterSubject');
        const filterDateFrom = document.getElementById('filterDateFrom');
        const filterDateTo = document.getElementById('filterDateTo');

        filterBtns.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                filterBtns.forEach(function(b) {
                    b.classList.remove('active');
                });
                this.classList.add('active');

                const range = this.getAttribute('data-range');
                const today = new Date();
                let daysBack = 30;
                if (range === '7d') {
                    daysBack = 7;
                } else if (range === '90d') {
                    daysBack = 90;
                }

                const fromDate = new Date(today);
                fromDate.setDate(fromDate.getDate() - daysBack);

                filterDateFrom.value = fromDate.toISOString().split('T')[0];
                filterDateTo.value = today.toISOString().split('T')[0];
                fetchAndRenderAnalytics();
            });
        });

        if (applyBtn) {
            applyBtn.addEventListener('click', function(e) {
                e.preventDefault();
                fetchAndRenderAnalytics();
            });
        }

        [filterClass, filterSubject, filterDateFrom, filterDateTo].forEach(function(el) {
            if (!el) {
                return;
            }
            el.addEventListener('change', function() {
                fetchAndRenderAnalytics();
            });
        });
    }

    function getFilters() {
        return {
            class_id: document.getElementById('filterClass') ? document.getElementById('filterClass').value : '',
            subject_id: document.getElementById('filterSubject') ? document.getElementById('filterSubject').value : '',
            date_from: document.getElementById('filterDateFrom') ? document.getElementById('filterDateFrom').value : '',
            date_to: document.getElementById('filterDateTo') ? document.getElementById('filterDateTo').value : ''
        };
    }

    async function fetchAndRenderAnalytics() {
        const loadingEl = document.getElementById('analyticsLoading');
        const errorEl = document.getElementById('analyticsError');

        loadingEl.classList.remove('d-none');
        errorEl.classList.add('d-none');
        errorEl.textContent = '';

        try {
            const params = new URLSearchParams();
            const filters = getFilters();
            Object.entries(filters).forEach(function(entry) {
                if (entry[1]) {
                    params.append(entry[0], entry[1]);
                }
            });

            const response = await fetch(endpoints.data + '?' + params.toString(), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch analytics data.');
            }

            const payload = await response.json();
            currentAnalyticsData = {
                classStats: payload.classStats || [],
                subjectStats: payload.subjectStats || [],
                mostAbsentStudents: payload.mostAbsentStudents || []
            };

            updateSummaryCards(payload.summary || {});
            updateTables(currentAnalyticsData);
            renderCharts(payload);
        } catch (error) {
            errorEl.textContent = error.message || 'Unexpected error while loading analytics data.';
            errorEl.classList.remove('d-none');
        } finally {
            loadingEl.classList.add('d-none');
        }
    }

    function updateSummaryCards(summary) {
        document.getElementById('statTotal').textContent = summary.totalAttendanceRecords ?? 0;
        document.getElementById('statRate').textContent = (summary.overallPercentage ?? 0) + '%';
        document.getElementById('statPresent').textContent = summary.presentCount ?? 0;
        document.getElementById('statAbsent').textContent = summary.absentCount ?? 0;
        document.getElementById('statLate').textContent = summary.lateCount ?? 0;
        document.getElementById('statExcused').textContent = summary.excusedCount ?? 0;
        document.getElementById('statClasses').textContent = summary.classCount ?? 0;
        document.getElementById('statSubjects').textContent = summary.subjectCount ?? 0;
    }

    function getStatusClass(percentage) {
        if (percentage >= 85) return 'excellent';
        if (percentage >= 75) return 'good';
        if (percentage >= 70) return 'fair';
        return 'poor';
    }

    function updateTables(data) {
        const classBody = document.getElementById('classStatsTable');
        const subjectBody = document.getElementById('subjectStatsTable');
        const absentBody = document.getElementById('absentStudentsTable');

        classBody.innerHTML = data.classStats.length ? data.classStats.map(function(stat) {
            return '<tr>' +
                '<td><strong>' + escapeHtml(stat.class) + '</strong></td>' +
                '<td class="text-end">' + stat.total + '</td>' +
                '<td class="text-end text-success">' + stat.present + '</td>' +
                '<td class="text-end text-danger">' + stat.absent + '</td>' +
                '<td class="text-end">' + stat.late + '</td>' +
                '<td class="text-end"><span class="status-badge status-' + getStatusClass(stat.percentage) + '">' + stat.percentage + '%</span></td>' +
                '</tr>';
        }).join('') : '<tr><td colspan="6" class="text-center py-4 text-muted">No class data available</td></tr>';

        subjectBody.innerHTML = data.subjectStats.length ? data.subjectStats.map(function(stat) {
            return '<tr>' +
                '<td><strong>' + escapeHtml(stat.subject) + '</strong></td>' +
                '<td class="text-end">' + stat.total + '</td>' +
                '<td class="text-end text-success">' + stat.present + '</td>' +
                '<td class="text-end text-danger">' + stat.absent + '</td>' +
                '<td class="text-end">' + stat.late + '</td>' +
                '<td class="text-end"><span class="status-badge status-' + getStatusClass(stat.percentage) + '">' + stat.percentage + '%</span></td>' +
                '</tr>';
        }).join('') : '<tr><td colspan="6" class="text-center py-4 text-muted">No subject data available</td></tr>';

        absentBody.innerHTML = data.mostAbsentStudents.length ? data.mostAbsentStudents.map(function(student) {
            const absenceClass = student.absence_percentage > 30 ? 'poor' : (student.absence_percentage > 15 ? 'fair' : 'good');
            return '<tr>' +
                '<td><strong>' + escapeHtml(student.name) + '</strong></td>' +
                '<td>' + escapeHtml(student.student_id) + '</td>' +
                '<td>' + escapeHtml(student.class) + '</td>' +
                '<td class="text-end">' + student.total_attendance + '</td>' +
                '<td class="text-end text-danger">' + student.absences + '</td>' +
                '<td class="text-end"><span class="status-badge status-' + absenceClass + '">' + student.absence_percentage + '%</span></td>' +
                '</tr>';
        }).join('') : '<tr><td colspan="6" class="text-center py-4 text-muted">No student data available</td></tr>';
    }

    function renderCharts(payload) {
        if (typeof ApexCharts === 'undefined') {
            return;
        }

        const statusSeries = [
            payload.statusDistribution?.present || 0,
            payload.statusDistribution?.absent || 0,
            payload.statusDistribution?.late || 0,
            payload.statusDistribution?.excused || 0,
        ];

        const trendLabels = (payload.dailyTrend || []).map(function(item) {
            return item.date;
        });
        const trendData = (payload.dailyTrend || []).map(function(item) {
            return item.percentage;
        });
        const classLabels = (payload.classStats || []).map(function(item) {
            return item.class;
        });
        const classData = (payload.classStats || []).map(function(item) {
            return item.percentage;
        });
        const subjectLabels = (payload.subjectStats || []).map(function(item) {
            return item.subject;
        });
        const subjectData = (payload.subjectStats || []).map(function(item) {
            return item.percentage;
        });

        destroyCharts();

        chartInstances.status = new ApexCharts(document.querySelector('#statusChart'), {
            chart: {
                type: 'donut',
                height: 350
            },
            series: statusSeries,
            labels: ['Present', 'Absent', 'Late', 'Excused'],
            colors: ['#28a745', '#dc3545', '#ffc107', '#17a2b8'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '75%'
                    }
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: 300
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        });
        chartInstances.status.render();

        chartInstances.trend = new ApexCharts(document.querySelector('#trendChart'), {
            chart: {
                type: 'area',
                height: 350,
                toolbar: {
                    show: true
                }
            },
            series: [{
                name: 'Attendance %',
                data: trendData
            }],
            xaxis: {
                categories: trendLabels
            },
            colors: ['#667eea'],
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
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: 300
                    }
                }
            }]
        });
        chartInstances.trend.render();

        chartInstances.classWise = new ApexCharts(document.querySelector('#classChart'), {
            chart: {
                type: 'bar',
                height: 400,
                toolbar: {
                    show: true
                }
            },
            series: [{
                name: 'Attendance %',
                data: classData
            }],
            xaxis: {
                categories: classLabels
            },
            colors: ['#667eea'],
            plotOptions: {
                bar: {
                    columnWidth: '60%',
                    borderRadius: 5
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return Number(val).toFixed(1) + '%';
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: 300
                    }
                }
            }]
        });
        chartInstances.classWise.render();

        chartInstances.subjectWise = new ApexCharts(document.querySelector('#subjectChart'), {
            chart: {
                type: 'bar',
                height: 400,
                toolbar: {
                    show: true
                }
            },
            series: [{
                name: 'Attendance %',
                data: subjectData
            }],
            xaxis: {
                categories: subjectLabels
            },
            colors: ['#28a745'],
            plotOptions: {
                bar: {
                    columnWidth: '60%',
                    borderRadius: 5
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return Number(val).toFixed(1) + '%';
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: 300
                    }
                }
            }]
        });
        chartInstances.subjectWise.render();
    }

    function destroyCharts() {
        Object.keys(chartInstances).forEach(function(key) {
            if (chartInstances[key]) {
                chartInstances[key].destroy();
                chartInstances[key] = null;
            }
        });
    }

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>'"]/g, function(char) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            };
            return map[char] || char;
        });
    }

    function getTableContext(tableId) {
        const tbody = document.getElementById(tableId);
        if (!tbody) {
            return null;
        }

        const table = tbody.closest('table');
        if (!table) {
            return null;
        }

        const headers = Array.from(table.querySelectorAll('thead th')).map(function(th) {
            return th.textContent.trim();
        });

        const rows = Array.from(tbody.querySelectorAll('tr')).map(function(tr) {
            return Array.from(tr.querySelectorAll('td')).map(function(td) {
                return td.textContent.trim();
            });
        }).filter(function(row) {
            return row.length > 0;
        });

        return {
            headers: headers,
            rows: rows
        };
    }

    function downloadTableAsCSV(tableId, filename) {
        const context = getTableContext(tableId);
        if (!context) {
            alert('Table not found!');
            return;
        }

        const csvLines = [];
        if (context.headers.length) {
            csvLines.push(context.headers.map(function(cell) {
                return '"' + String(cell).replace(/"/g, '""') + '"';
            }).join(','));
        }

        context.rows.forEach(function(row) {
            csvLines.push(row.map(function(cell) {
                return '"' + String(cell).replace(/"/g, '""') + '"';
            }).join(','));
        });

        const csvContent = '\ufeff' + csvLines.join('\n');
        const blob = new Blob([csvContent], {
            type: 'text/csv;charset=utf-8;'
        });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        link.click();
        URL.revokeObjectURL(link.href);
    }

    function downloadTableAsExcel(tableId, filename) {
        const context = getTableContext(tableId);
        if (!context) {
            alert('Table not found!');
            return;
        }

        if (typeof XLSX === 'undefined') {
            console.warn('XLSX library is not available. Falling back to CSV download.');
            const fallback = filename.replace(/\.xlsx$/i, '.csv');
            downloadTableAsCSV(tableId, fallback);
            return;
        }

        const data = [];
        if (context.headers.length) {
            data.push(context.headers);
        }
        context.rows.forEach(function(row) {
            data.push(row);
        });

        const ws = XLSX.utils.aoa_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Attendance');

        ws['!cols'] = context.headers.map(function() {
            return {
                wch: 16
            };
        });

        XLSX.writeFile(wb, filename);
    }
</script>
@endpush

@endsection