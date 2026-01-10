@extends('layouts.app')

@section('title', 'Teacher Profile')

@section('content')

<div class="card mb-4 p-4">
<div class="d-flex justify-content-between align-items-center">
<h6 class="d-flex align-items-center mb-0 text-uppercase fw-bold">
<span class="text-muted">Teachers</span>
<i data-lucide="chevron-right" class="mx-2" style="width: 16px;"></i>
<span>View Profile</span>
<i data-lucide="chevron-right" class="mx-2" style="width: 16px;"></i>
<span class="text-primary">{{ $teacher->first_name }} {{ $teacher->last_name }}</span>
</h6>
<div>
<a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="btn btn-outline-primary btn-sm me-2">
<i data-lucide="pencil" class="me-1" style="width: 14px;"></i> Edit Profile
</a>
<a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary btn-sm">
<i data-lucide="arrow-left" class="me-1" style="width: 14px;"></i> Back to List
</a>
</div>
</div>
</div>

@include('includes.message')

<div class="row">
<!-- Sidebar: Quick Overview -->
<div class="col-md-4">
<div class="card mb-4">
<div class="card-body text-center py-5">
<div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 100px; height: 100px;">
<i data-lucide="user" class="text-secondary" style="width: 50px; height: 50px;"></i>
</div>
<h5 class="fw-bold mb-1">{{ $teacher->first_name }} {{ $teacher->last_name }}</h5>
<p class="text-muted mb-3">{{ $teacher->staff_id ?? 'No Staff ID' }}</p>

            @if($teacher->status == 'active')
                <span class="badge bg-success px-3 py-2">Active Staff</span>
            @else
                <span class="badge bg-danger px-3 py-2">Inactive</span>
            @endif
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h6 class="card-title mb-3 d-flex align-items-center">
                <i data-lucide="info" class="me-2 text-primary" style="width: 18px;"></i> System Log
            </h6>
            <div class="small">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Account Created:</span>
                    <span class="fw-medium">{{ $teacher->created_at->format('M d, Y') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Last Updated:</span>
                    <span class="fw-medium">{{ $teacher->updated_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content: Detailed Information -->
<div class="col-md-8">
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="card-title mb-4 border-bottom pb-2">Full Profile Details</h6>
            
            <div class="row mb-4">
                <div class="col-sm-4 text-muted">First Name</div>
                <div class="col-sm-8 fw-bold">{{ $teacher->first_name }}</div>
            </div>

            <div class="row mb-4">
                <div class="col-sm-4 text-muted">Last Name</div>
                <div class="col-sm-8 fw-bold">{{ $teacher->last_name }}</div>
            </div>

            <div class="row mb-4">
                <div class="col-sm-4 text-muted">Email Address</div>
                <div class="col-sm-8">
                    <a href="mailto:{{ $teacher->email }}" class="text-decoration-none d-flex align-items-center">
                        <i data-lucide="mail" class="me-2" style="width: 14px;"></i> {{ $teacher->email }}
                    </a>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-sm-4 text-muted">Phone Number</div>
                <div class="col-sm-8">
                    {{ $teacher->phone ?? 'Not Provided' }}
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-sm-4 text-muted">Residential Address</div>
                <div class="col-sm-8">
                    {{ $teacher->address ?? 'No address information available.' }}
                </div>
            </div>

            <div class="row mb-0">
                <div class="col-sm-4 text-muted">Staff Identifier</div>
                <div class="col-sm-8">
                    <code class="bg-light px-2 py-1 rounded">{{ $teacher->staff_id ?? 'N/A' }}</code>
                </div>
            </div>
        </div>
    </div>

    <!-- Placeholder for future modules -->
    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-none">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase fw-bold mb-2">Assigned Classes</h6>
                    <p class="mb-0 small">No classes assigned yet.</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-none">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase fw-bold mb-2">Assigned Subjects</h6>
                    <p class="mb-0 small">No subjects assigned yet.</p>
                </div>
            </div>
        </div>
    </div>
</div>


</div>

@endsection