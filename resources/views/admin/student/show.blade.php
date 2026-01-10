@extends('layouts.app')

@section('title', 'Student Profile')

@section('content')

<div class="card mb-4 p-4">
<div class="d-flex align-items-center justify-content-between">
<h6 class="d-flex align-items-center mb-0 text-uppercase fw-bold">
<span class="text-muted">Students</span>
<i data-lucide="chevron-right" class="mx-2" style="width: 16px;"></i>
<span>Profile View</span>
<i data-lucide="chevron-right" class="mx-2" style="width: 16px;"></i>
<span class="text-primary">{{ $student->first_name }} {{ $student->last_name }}</span>
</h6>
<div>
<a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-outline-primary btn-sm">
<i data-lucide="pencil" class="me-1" style="width: 14px;"></i> Edit Profile
</a>
<a href="{{ route('admin.students.index') }}" class="btn btn-light btn-sm border ms-2">
<i data-lucide="arrow-left" class="me-1" style="width: 14px;"></i> Back to List
</a>
</div>
</div>
</div>

<div class="row">
<!-- Left Column: Profile Card & Academic Info -->
<div class="col-md-4">
<div class="card mb-4 overflow-hidden">
<div class="bg-primary p-4 text-center">
<div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 100px; height: 100px;">
<i data-lucide="user" class="text-primary" style="width: 50px; height: 50px;"></i>
</div>
<h5 class="text-white mb-1">{{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}</h5>
<p class="text-white-50 mb-0">ID: {{ $student->student_id }}</p>
</div>
<div class="card-body p-0">
<ul class="list-group list-group-flush">
<li class="list-group-item d-flex justify-content-between align-items-center p-3">
<span class="text-muted small text-uppercase fw-bold">Status</span>
@if($student->status == 'active')
<span class="badge bg-success">Active</span>
@else
<span class="badge bg-secondary">Inactive</span>
@endif
</li>
<li class="list-group-item d-flex justify-content-between align-items-center p-3">
<span class="text-muted small text-uppercase fw-bold">Class</span>
<span class="fw-bold">{{ $levelData->class->name ?? 'N/A' }}</span>
</li>
<li class="list-group-item d-flex justify-content-between align-items-center p-3">
<span class="text-muted small text-uppercase fw-bold">Academic Year</span>
<span>{{ $levelData->academicYear->name ?? 'N/A' }}</span>
</li>
</ul>
</div>
</div>

    <div class="card border-0 shadow-none">
        <div class="card-body">
            <h6 class="card-title d-flex align-items-center mb-3">
                <i data-lucide="clock" class="me-2 text-muted" style="width: 18px;"></i> System Logs
            </h6>
            <div class="small">
                <p class="mb-1 text-muted">Created:</p>
                <p class="fw-bold mb-3">{{ $student->created_at->format('M d, Y h:i A') }}</p>
                <p class="mb-1 text-muted">Last Updated:</p>
                <p class="fw-bold mb-0">{{ $student->updated_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Right Column: Detailed Information -->
<div class="col-md-8">
    <!-- Personal Information Section -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold d-flex align-items-center text-primary">
                <i data-lucide="info" class="me-2" style="width: 18px;"></i> Personal Details
            </h6>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-sm-4 text-muted">Full Name:</div>
                <div class="col-sm-8 fw-bold">
                    {{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }} 
                    @if($student->other_name) ({{ $student->other_name }}) @endif
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-sm-4 text-muted">Gender:</div>
                <div class="col-sm-8 text-capitalize">{{ $student->gender }}</div>
            </div>
            <div class="row mb-4">
                <div class="col-sm-4 text-muted">Date of Birth:</div>
                <div class="col-sm-8">{{ $student->date_of_birth ? $student->date_of_birth : 'Not Provided' }}</div>
            </div>
            <div class="row mb-0">
                <div class="col-sm-4 text-muted">Residential Address:</div>
                <div class="col-sm-8">{{ $student->address ?? 'Not Provided' }}</div>
            </div>
        </div>
    </div>

    <!-- Parent/Guardian Information Section -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold d-flex align-items-center text-primary">
                <i data-lucide="users" class="me-2" style="width: 18px;"></i> Parent / Guardian Details
            </h6>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-sm-4 text-muted">Parent Name:</div>
                <div class="col-sm-8 fw-bold">{{ $student->parent_name ?? 'N/A' }}</div>
            </div>
            <div class="row mb-4">
                <div class="col-sm-4 text-muted">Phone Number:</div>
                <div class="col-sm-8">
                    @if($student->parent_phone)
                        <a href="tel:{{ $student->parent_phone }}" class="text-decoration-none">{{ $student->parent_phone }}</a>
                    @else
                        N/A
                    @endif
                </div>
            </div>
            <div class="row mb-0">
                <div class="col-sm-4 text-muted">Email Address:</div>
                <div class="col-sm-8">
                    @if($student->parent_email)
                        <a href="mailto:{{ $student->parent_email }}" class="text-decoration-none">{{ $student->parent_email }}</a>
                    @else
                        N/A
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Academic History Placeholder -->
    <div class="card border-0 p-4 text-center">
        <i data-lucide="bar-chart-2" class="mx-auto mb-2 text-muted" style="width: 40px; height: 40px; opacity: 0.3;"></i>
        <h6 class="text-muted">Academic History & Performance Tracking</h6>
        <p class="small text-muted mb-0">Coming soon: Student grades, attendance records, and behavioral reports.</p>
    </div>
</div>


</div>

@endsection