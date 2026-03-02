@extends('layouts.app')

@section('title', 'Student Report')

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">Student Report</h6>
        <div>
            <a href="{{ route('reports.students', request()->query()) }}" class="btn btn-primary btn-sm me-2">
                <i data-lucide="refresh-cw" style="width: 14px;" class="me-1"></i>Refresh
            </a>
            <button onclick="window.print()" class="btn btn-success btn-sm">
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
                <h6 class="card-title text-uppercase fw-bold small mb-2">Total Students</h6>
                <h3 class="mb-0 text-primary">{{ $totalStudents }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-uppercase fw-bold small mb-2">Male Students</h6>
                <h3 class="mb-0 text-info">{{ $maleStudents }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-uppercase fw-bold small mb-2">Female Students</h6>
                <h3 class="mb-0 text-warning">{{ $femaleStudents }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-uppercase fw-bold small mb-2">Active Students</h6>
                <h3 class="mb-0 text-success">{{ $activeStudents }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <h6 class="card-title fw-bold mb-4">Filters</h6>

        <form method="GET" action="{{ route('reports.students') }}" class="filter-form">
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
                    <label class="form-label fw-bold">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="">All Genders</option>
                        @foreach($genders as $gender)
                        <option value="{{ $gender }}" {{ request('gender') == $gender ? 'selected' : '' }}>
                            {{ $gender }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
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

<!-- Students Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th width="20%">
                        <a href="{{ route('reports.students', array_merge(request()->query(), ['sort_by' => 'first_name', 'sort_order' => request('sort_order') == 'asc' && request('sort_by') == 'first_name' ? 'desc' : 'asc'])) }}">
                            Name
                            @if(request('sort_by') == 'first_name')
                            <i data-lucide="{{ request('sort_order') == 'asc' ? 'arrow-up' : 'arrow-down' }}" style="width: 14px;"></i>
                            @endif
                        </a>
                    </th>
                    <th width="12%">Student ID</th>
                    <th width="10%">Gender</th>
                    <th width="15%">Class</th>
                    <th width="15%">DOB</th>
                    <th width="12%">Status</th>
                    <th width="16%">Contact</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr>
                    <td>
                        <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                        @if($student->middle_name)
                        <br><small class="text-muted">{{ $student->middle_name }}</small>
                        @endif
                    </td>
                    <td>{{ $student->student_id }}</td>
                    <td>
                        <span class="badge {{ $student->gender == 'Male' ? 'bg-info' : 'bg-danger' }}">
                            {{ $student->gender }}
                        </span>
                    </td>
                    <td>
                        @if($student->latestLevel)
                        {{ $student->latestLevel->classModel->name ?? 'N/A' }}
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('M d, Y') : '-' }}</td>
                    <td>
                        <span class="badge {{ $student->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                            {{ ucfirst($student->status) }}
                        </span>
                    </td>
                    <td>
                        <small>
                            <i class="bi bi-telephone"></i> {{ $student->parent_phone ?? '-' }}<br>
                            <i class="bi bi-envelope"></i> {{ $student->parent_email ? Str::limit($student->parent_email, 20) : '-' }}
                        </small>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        No students found matching the criteria.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $students->links() }}
</div>

@endsection