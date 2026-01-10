@extends('layouts.app')

@section('title', 'Add School Session')

@section('content')

<div class="card mb-4 p-4">
<h6 class="d-flex align-items-center mb-0">
<span class="text-muted">School Sessions</span>
<i data-lucide="chevron-right" class="mx-2" style="width: 16px;"></i>
<span>Add Session</span>
</h6>
</div>

@include('includes.message')

<div class="row">
<div class="col-md-6 grid-margin stretch-card">
<div class="card">
<div class="card-body">
<div class="d-flex justify-content-between align-items-center mb-4">
<h6 class="card-title mb-0">Create New Session</h6>
<a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.sessions.index') }}">
<i data-lucide="arrow-left" class="me-1" style="width: 14px;"></i> Back
</a>
</div>

            <form action="{{ route('admin.sessions.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label">Session Name</label>
                    <input type="text" name="name" id="name" 
                           class="form-control @error('name') is-invalid @enderror" 
                           placeholder="e.g. 2023/2024" 
                           value="{{ old('name') }}" required autofocus>
                    <div class="form-text">Example: 2023/2024 Academic Session</div>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="save" class="me-1" style="width: 18px;"></i> Save Session
                    </button>
                    <a href="{{ route('admin.sessions.index') }}" class="btn btn-light ms-1">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="col-md-4">
    <div class="card border-0">
        <div class="card-body">
            <h6 class="card-title d-flex align-items-center">
                <i data-lucide="help-circle" class="me-2 text-info" style="width: 18px;"></i> Help
            </h6>
            <p class="text-muted small">
                Enter the name for the academic year. By default, newly created sessions are set to <strong>Inactive</strong>. 
            </p>
            <p class="text-muted small">
                You can activate the session from the list view once you are ready to start enrollments for that period.
            </p>
        </div>
    </div>
</div>


</div>
@endsection