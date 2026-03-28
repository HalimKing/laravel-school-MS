@extends('layouts.app')

@section('title', 'Attendance Report')

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">Attendance Report</h6>
        <div>
            <a href="{{ route('admin.attendance.create') }}" class="btn btn-success btn-sm me-2">
                <i data-lucide="plus" style="width: 14px;" class="me-1"></i>Take Attendance
            </a>
            <button onclick="window.print()" class="btn btn-primary btn-sm">
                <i data-lucide="printer" style="width: 14px;" class="me-1"></i>Print
            </button>
        </div>
    </div>
</div>

@include('includes.message')

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-uppercase fw-bold small mb-2">Total Records</h6>
                <h3 class="mb-0 text-primary">{{ $totalRecords }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-uppercase fw-bold small mb-2">Present</h6>
                <h3 class="mb-0 text-success">{{ $presentCount }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-uppercase fw-bold small mb-2">Absent</h6>
                <h3 class="mb-0 text-danger">{{ $absentCount }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-uppercase fw-bold small mb-2">Avg Attendance %</h6>
                <h3 class="mb-0 text-info">{{ $averageAttendancePercentage }}%</h3>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <h6 class="card-title fw-bold mb-4">Filters</h6>

        <form method="GET" action="{{ route('reports.attendance') }}" class="filter-form">
            <div class="row">
                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Search</label>
                    <input type="text" name="search" class="form-control"
                        placeholder="Name or ID..." value="{{ request('search') }}">
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Class</label>
                    <select name="class_id" class="form-select">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Academic Year</label>
                    <select name="academic_year_id" class="form-select">
                        <option value="">All Years</option>
                        @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                            {{ $year->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Period</label>
                    <select name="academic_period_id" class="form-select">
                        <option value="">All Periods</option>
                        @foreach($academicPeriods as $period)
                        <option value="{{ $period->id }}" {{ request('academic_period_id') == $period->id ? 'selected' : '' }}>
                            {{ $period->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">From Date</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>

                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                        <i data-lucide="search" style="width: 14px;" class="me-1"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Attendance Records Table -->
<div class="card">
    <div class="card-header bg-light">
        <h6 class="card-title fw-bold mb-0">Attendance Records</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0" id="attendanceTable">
            <thead class="table-light">
                <tr>
                    <th width="12%">Date</th>
                    <th width="20%">Student</th>
                    <th width="15%">Class</th>
                    <th width="13%">Period</th>
                    <th width="13%">Status</th>
                    <th width="27%">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                <tr>
                    <td>{{ $record->attendance_date ? \Carbon\Carbon::parse($record->attendance_date)->format('M d, Y') : 'N/A' }}</td>
                    <td>{{ $record->student->first_name ?? '' }} {{ $record->student->last_name ?? '' }}</td>
                    <td>{{ $record->student->latestLevel?->class?->name ?? 'N/A' }}</td>
                    <td>{{ $record->academicPeriod?->name ?? 'N/A' }}</td>
                    <td>
                        @if($record->status === 'present')
                        <span class="badge bg-success">Present</span>
                        @elseif($record->status === 'absent')
                        <span class="badge bg-danger">Absent</span>
                        @else
                        <span class="badge bg-warning">{{ ucfirst($record->status) }}</span>
                        @endif
                    </td>
                    <td>{{ $record->remarks ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        No attendance records found matching the criteria.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $records->links() }}
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if ($.fn.DataTable.isDataTable('#attendanceTable')) {
            $('#attendanceTable').DataTable().destroy();
        }

        $('#attendanceTable').DataTable({
            pageLength: 50,
            responsive: true,
            order: [
                [0, 'desc']
            ],
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            language: {
                searchPlaceholder: 'Search by name, date, etc...'
            }
        });
    });
</script>
@endpush

@endsection