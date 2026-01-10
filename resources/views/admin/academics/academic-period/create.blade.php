@extends('layouts.app')

@section('title', 'Add Academic Period')

@section('content')

<div class="card mb-4 p-4">
<h6 class="d-flex align-items-center mb-0 text-uppercase fw-bold">
<span class="text-muted">Academic Periods</span>
<i data-lucide="chevron-right" class="mx-2" style="width: 16px;"></i>
<span>Add Period</span>
</h6>
</div>

@include('includes.message')

<div class="row">
<div class="col-md-6 grid-margin stretch-card">
<div class="card">
<div class="card-body">
<div class="d-flex justify-content-between align-items-center mb-4">
<h6 class="card-title mb-0">Create New Period</h6>
<a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.academics.academic-periods.index') }}">
<i data-lucide="arrow-left" class="me-1" style="width: 14px;"></i> Back
</a>
</div>

            <form action="{{ route('admin.academics.academic-periods.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label">Period Name</label>
                    <input type="text" name="name" id="name" 
                           class="form-control @error('name') is-invalid @enderror" 
                           placeholder="e.g. First Term, Second Semester" 
                           value="{{ old('name') }}" required autofocus>
                    <div class="form-text">Example: First Term or Semester 1</div>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="save" class="me-1" style="width: 18px;"></i> Save Period
                    </button>
                    <a href="{{ route('admin.academics.academic-periods.index') }}" class="btn btn-light ms-1">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="col-md-4">
    <div class="card border-0">
        <div class="card-body">
            <h6 class="card-title d-flex align-items-center">
                <i data-lucide="help-circle" class="me-2 text-info" style="width: 18px;"></i> Information
            </h6>
            <p class="text-muted small">
                Academic periods represent the divisions of your school sessions (e.g., Terms, Semesters, or Quarters).
            </p>
            <p class="text-muted small">
                Once created, you can link these periods to specific school sessions to manage grading and attendance.
            </p>
        </div>
    </div>
</div>


</div>
@endsection