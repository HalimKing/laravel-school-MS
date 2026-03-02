@extends('layouts.app')

@section('title', 'Student Attendance Report')

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">Student Attendance Report</h6>
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

@if($selectedStudent && $stats)
<!-- Student Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-uppercase fw-bold small mb-2">Total Classes</h6>
                <h3 class="mb-0 text-primary">{{ $stats['total_classes'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-uppercase fw-bold small mb-2">Present</h6>
                <h3 class="mb-0 text-success">{{ $stats['present'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-uppercase fw-bold small mb-2">Absent</h6>
                <h3 class="mb-0 text-danger">{{ $stats['absent'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-uppercase fw-bold small mb-2">Attendance %</h6>
                <h3 class="mb-0 text-warning">{{ $stats['attendance_percentage'] }}%</h3>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <h6 class="card-title fw-bold mb-4">Filters</h6>

        <form method="GET" action="{{ route('admin.attendance.student-report') }}">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Student</label>
                    <select name="student_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Select a Student</option>
                        @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->first_name }} {{ $student->last_name }}
                            <small>({{ $student->student_id }})</small>
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
                    <label class="form-label fw-bold">From Date</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>

                <div class="col-md-1 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i data-lucide="search" style="width: 14px;"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Student Information Card -->
@if($selectedStudent)
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-2">
                    <strong>Student Name:</strong> {{ $selectedStudent->first_name }} {{ $selectedStudent->last_name }}
                </p>
                <p class="mb-2">
                    <strong>Student ID:</strong> {{ $selectedStudent->student_id }}
                </p>
                <p class="mb-0">
                    <strong>Email:</strong> {{ $selectedStudent->email ?? 'N/A' }}
                </p>
            </div>
            <div class="col-md-6">
                @if($selectedStudent->levelData->first())
                <p class="mb-2">
                    <strong>Current Class:</strong> {{ $selectedStudent->levelData->first()->classModel->name ?? 'N/A' }}
                </p>
                <p class="mb-2">
                    <strong>Academic Year:</strong> {{ $selectedStudent->levelData->first()->academicYear->name ?? 'N/A' }}
                </p>
                <p class="mb-0">
                    <strong>Phone:</strong> {{ $selectedStudent->contact_number ?? 'N/A' }}
                </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Attendance Records Table -->
<div class="card">
    <div class="card-header bg-light">
        <h6 class="card-title fw-bold mb-0">Attendance History</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th width="18%">Date</th>
                    <th width="18%">Class</th>
                    <th width="18%">Subject</th>
                    <th width="16%">Status</th>
                    <th width="30%">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                <tr>
                    <td>
                        <strong>{{ $record->attendance_date->format('M d, Y') }}</strong>
                        <br><small class="text-muted">{{ $record->attendance_date->format('D') }}</small>
                    </td>
                    <td>{{ $record->classModel->name ?? 'N/A' }}</td>
                    <td>{{ $record->subject->name ?? 'General' }}</td>
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
                        {{ $selectedStudent ? 'No attendance records found for this student.' : 'Select a student to view their attendance history.' }}
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