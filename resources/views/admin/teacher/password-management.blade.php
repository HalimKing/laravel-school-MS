@extends('layouts.app')

@section('title', 'Teacher Password Management')

@section('content')

<div class="card mb-4 p-4">
<h6 class="d-flex align-items-center mb-0 text-uppercase fw-bold">
<span class="text-muted">Teachers</span>
<i data-lucide="chevron-right" class="mx-2" style="width: 16px;"></i>
<span>Password Management</span>
</h6>
</div>

@include('includes.message')

<div class="row justify-content-center">
<div class="col-md-8">
<!-- Search Section -->
<div class="card mb-4 border-primary">
<div class="card-body">
<h6 class="card-title mb-3 d-flex align-items-center">
<i data-lucide="search" class="me-2 text-primary" style="width: 18px;"></i> Search Teacher
</h6>
<form action="{{ route('admin.teacher.password.search') }}" method="GET" class="row g-3">
<div class="col-md-9">
<input type="text" name="search_query" class="form-control"
placeholder="Enter Staff ID or Email Address..."
value="{{ request('search_query') }}" required>
</div>
<div class="col-md-3">
<button type="submit" class="btn btn-primary w-100">
Search
</button>
</div>
</form>
</div>
</div>

    @if(isset($teacher))
        <!-- Reset Form Section (Visible only after search) -->
        <div class="card shadow-sm">
            <div class="card-header bg-transparent border-bottom py-3">
                <div class="d-flex align-items-center">
                    <div class="bg-light rounded-circle p-2 me-3">
                        <i data-lucide="user" class="text-primary" style="width: 20px;"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">{{ $teacher->first_name }} {{ $teacher->last_name }}</h6>
                        <small class="text-muted">{{ $teacher->email }} | {{ $teacher->staff_id }}</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.teacher.password.update', $teacher->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i data-lucide="key" style="width: 16px;"></i></span>
                            <input type="password" name="password" id="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   placeholder="Enter new password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-semibold">Confirm New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i data-lucide="lock" style="width: 16px;"></i></span>
                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                   class="form-control" placeholder="Confirm new password" required>
                        </div>
                    </div>

                    <div class="alert alert-warning d-flex align-items-start border-0 bg-light-warning">
                        <i data-lucide="alert-triangle" class="me-2 mt-1 text-warning" style="width: 18px;"></i>
                        <div class="small">
                            Warning: This will immediately change the teacher's login credentials. They will be logged out of all active sessions.
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('admin.teacher.password.index') }}" class="btn btn-light me-2">Cancel</a>
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to reset this password?')">
                            <i data-lucide="refresh-cw" class="me-1" style="width: 16px;"></i> Reset Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @elseif(request('search_query'))
        <!-- No Results State -->
        <div class="text-center py-5">
            <i data-lucide="user-x" class="text-muted mb-3" style="width: 48px; height: 48px; opacity: 0.5;"></i>
            <h5 class="text-muted">Teacher Not Found</h5>
            <p class="text-muted small">We couldn't find any staff member matching "{{ request('search_query') }}"</p>
        </div>
    @endif
</div>


</div>
@endsection