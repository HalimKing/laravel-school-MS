@extends('layouts.app')

@section('title', 'Class Attendance Report')

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">Class Attendance Report</h6>
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
                <h3 class="mb-0 text-primary">{{ $stats['total_records'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-uppercase fw-bold small mb-2">Present</h6>
                <h3 class="mb-0 text-success">{{ $stats['present'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-uppercase fw-bold small mb-2">Absent</h6>
                <h3 class="mb-0 text-danger">{{ $stats['absent'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-uppercase fw-bold small mb-2">Late</h6>
                <h3 class="mb-0 text-warning">{{ $stats['late'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <h6 class="card-title fw-bold mb-4">Filters</h6>

        <form method="GET" action="{{ route('admin.attendance.class-report') }}">
            <div class="row">
                <div class="col-md-3 mb-3">
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

                <div class="col-md-3 mb-3">
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
                    <label class="form-label fw-bold">Subject</label>
                    <select name="subject_id" class="form-select">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
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
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i data-lucide="search" style="width: 14px;" class="me-1"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Attendance Records Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th width="12%">Date</th>
                    <th width="22%">Student</th>
                    <th width="15%">Class</th>
                    <th width="18%">Subject</th>
                    <th width="13%">Status</th>
                    <th width="20%">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                <tr>
                    <td>
                        <strong>{{ $record->attendance_date->format('M d, Y') }}</strong>
                    </td>
                    <td>
                        <strong>{{ $record->student->first_name }} {{ $record->student->last_name }}</strong>
                        <br><small class="text-muted">{{ $record->student->student_id }}</small>
                    </td>
                    <td>{{ $record->classModel->name ?? 'N/A' }}</td>
                    <td>
                        @if($record->subject)
                        <span class="badge bg-light text-dark">{{ $record->subject->name }}</span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @php
                        $statusColors = [
                        'present' => 'success',
                        'absent' => 'danger',
                        'late' => 'warning',
                        'excused' => 'info'
                        ];
                        @endphp
                        <span class="badge bg-{{ $statusColors[$record->status] ?? 'secondary' }}">
                            {{ ucfirst($record->status) }}
                        </span>
                    </td>
                    <td>
                        <small>{{ $record->remarks ?? '-' }}</small>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        No attendance records found.
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

@endsection