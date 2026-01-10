@extends('layouts.app')

@section('title', 'Edit Academic Period')

@section('content')

<div class="card mb-4 p-4">
<h6 class="d-flex align-items-center mb-0 text-uppercase fw-bold">
<span class="text-muted">Academic Periods</span>
<i data-lucide="chevron-right" class="mx-2" style="width: 16px;"></i>
<span>Edit Period</span>
<i data-lucide="chevron-right" class="mx-2" style="width: 16px;"></i>
<span class="text-primary">{{ $academicPeriod->name }}</span>
</h6>
</div>

@include('includes.message')

<div class="row">
<div class="col-md-6 grid-margin stretch-card">
<div class="card">
<div class="card-body">
<div class="d-flex justify-content-between align-items-center mb-4">
<h6 class="card-title mb-0">Modify Period</h6>
<a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.academics.academic-periods.index') }}">
<i data-lucide="arrow-left" class="me-1" style="width: 14px;"></i> Back
</a>
</div>

            <form action="{{ route('admin.academics.academic-periods.update', $academicPeriod->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="name" class="form-label">Period Name</label>
                    <input type="text" name="name" id="name" 
                           class="form-control @error('name') is-invalid @enderror" 
                           placeholder="e.g. First Term, Second Semester" 
                           value="{{ old('name', $academicPeriod->name) }}" required autofocus>
                    <div class="form-text">Update the name for this academic period.</div>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">
                        <i data-lucide="check-circle" class="me-1" style="width: 18px;"></i> Update Period
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
                <i data-lucide="info" class="me-2 text-info" style="width: 18px;"></i> Information
            </h6>
            <p class="text-muted small">
                You are currently editing the <strong>{{ $academicPeriod->name }}</strong> period.
            </p>
            <p class="text-muted small">
                Changes made here will be reflected across all school sessions that use this period name. Ensure any renaming aligns with your academic records.
            </p>
            <hr>
            <p class="text-muted small">
                <strong>Last Updated:</strong> {{ $academicPeriod->updated_at->diffForHumans() }}
            </p>
        </div>
    </div>
</div>


</div>

@endsection