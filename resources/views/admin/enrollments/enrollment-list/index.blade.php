@extends('layouts.app')

@section('title', 'Enrollment List')

@section('content')

<div class="card mb-4 p-4">
<div class="d-flex align-items-center justify-content-between">
<h6 class="d-flex align-items-center mb-0 text-uppercase fw-bold">
<span class="text-muted">Students</span>
<i data-lucide="chevron-right" class="mx-2" style="width: 16px;"></i>
<span>Enrollment List</span>
</h6>
@if(isset($students) && $students->count() > 0)
<div class="dropdown">
<button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
<i data-lucide="download" class="me-1" style="width: 14px;"></i> Download List
</button>
<ul class="dropdown-menu dropdown-menu-end">
<li>
<a class="dropdown-item" href="{{ route('admin.enrollments.enrollment-list.export', ['type' => 'csv', 'year' => request('academic_year'), 'class' => request('class')]) }}">
<i data-lucide="file-text" class="me-2 text-muted" style="width: 14px;"></i> Export as CSV
</a>
</li>
<li>
<a class="dropdown-item" href="{{ route('admin.enrollments.enrollment-list.export', ['type' => 'pdf', 'year' => request('academic_year'), 'class' => request('class')]) }}">
<i data-lucide="file-type-2" class="me-2 text-muted" style="width: 14px;"></i> Export as PDF
</a>
</li>
</ul>
</div>
@endif
</div>
</div>

<!-- Search & Filter Card -->

<div class="card mb-4 border-0 shadow-sm">
<div class="card-body">
<form action="{{ route('admin.enrollments.enrollment-list.index') }}" method="GET" class="row g-3">
<div class="col-md-5">
<label class="form-label small fw-bold">Academic Year</label>
<select name="academic_year" class="form-select" required>
<option value="">Select Academic Year</option>
@foreach($academicYears as $year)
<option value="{{ $year->id }}" {{ request('academic_year') == $year->id ? 'selected' : '' }}>
{{ $year->name }}
</option>
@endforeach
</select>
</div>
<div class="col-md-5">
<label class="form-label small fw-bold">Class</label>
<select name="class" class="form-select" required>
<option value="">Select Class</option>
@foreach($classes as $class)
<option value="{{ $class->id }}" {{ request('class') == $class->id ? 'selected' : '' }}>
{{ $class->name }}
</option>
@endforeach
</select>
</div>
<div class="col-md-2 d-flex align-items-end">
<button type="submit" class="btn btn-primary w-100">
<i data-lucide="search" class="me-1" style="width: 16px;"></i> Filter
</button>
</div>
</form>
</div>
</div>

@if(isset($students))

<div class="card border-0 shadow-sm">
<div class="card-body">
<div class="table-responsive">
<table class="table table-hover align-middle">
<thead class="table-light">
<tr>
<th class="py-3">Student ID</th>
<th class="py-3">Full Name</th>
<th class="py-3">Gender</th>
<th class="py-3">Academic Year</th>
<th class="py-3">Class</th>
</tr>
</thead>
<tbody>
@forelse($students as $student)
<tr>
<td class="fw-bold">{{ $student->student->student_id }}</td>
<td style="text-transform: capitalize;">{{ $student->student->first_name }} {{ $student->student->last_name }}  {{ $student->student->other_name }}</td>
<td class="text-capitalize">{{ $student->student->gender }}</td>
<td>{{ $student->academicYear->name ?? 'N/A' }}</td>
<td>{{ $student->class->name ?? 'N/A' }}</td>

</tr>
@empty
<tr>
<td colspan="6" class="text-center py-5">
<div class="text-muted">
<i data-lucide="users-2" class="mb-3 opacity-25" style="width: 48px; height: 48px;"></i>
<p class="mb-0">No students found for this specific filter.</p>
<small>Try selecting a different academic year or class.</small>
</div>
</td>
</tr>
@endforelse
</tbody>
</table>
</div>

    @if($students instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-4">
            {{ $students->appends(request()->query())->links() }}
        </div>
    @endif
</div>


</div>
@else
<div class="text-center py-5 rounded shadow-sm">
<i data-lucide="filter-x" class="text-muted mb-3" style="width: 50px; height: 50px; opacity: 0.2;"></i>
<h5 class="text-muted">Select filters to view enrollment list</h5>
<p class="text-muted small">Choose an Academic Year and Class above to generate the list.</p>
</div>
@endif

@endsection