@extends('layouts.app')

@section('title', 'Edit Fee Structure')

@section('content')

<div class="card mb-4 p-4">
<h6 class="d-flex align-items-center mb-0 text-uppercase fw-bold">
<span class="text-muted">Fee Management</span>
<i data-lucide="chevron-right" class="mx-2" style="width: 16px;"></i>
<span>Edit Fee Configuration</span>
</h6>
</div>

@include('includes.message')

<div class="row">
<div class="col-md-8 grid-margin stretch-card">
<div class="card border-0 shadow-sm">
<div class="card-body">
<div class="d-flex justify-content-between align-items-center mb-4">
<h6 class="card-title mb-0">Update Fee Configuration</h6>
<a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.fee-management.fees.index') }}">
<i data-lucide="arrow-left" class="me-1" style="width: 14px;"></i> Back to List
</a>
</div>

            <form action="{{ route('admin.fee-management.fees.update', $fee->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="fee_category_id" class="form-label">Fee Category <span class="text-danger">*</span></label>
                    <select name="fee_category_id" id="fee_category_id" class="form-select @error('fee_category_id') is-invalid @enderror" required>
                        <option value="">Select Category</option>
                        @foreach($feeCategories as $category)
                            <option value="{{ $category->id }}" {{ (old('fee_category_id', $fee->fee_category_id) == $category->id) ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('fee_category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="academic_year_id" class="form-label">Academic Year <span class="text-danger">*</span></label>
                        <select name="academic_year_id" id="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                            <option value="">Select Year</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ (old('academic_year_id', $fee->academic_year_id) == $year->id) ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="academic_period_id" class="form-label">Academic Period</label>
                        <select name="academic_period_id" id="academic_period_id" class="form-select @error('academic_period_id') is-invalid @enderror">
                            <option value="">Select Period</option>
                            @foreach($academicPeriods as $period)
                                <option value="{{ $period->id }}" {{ (old('academic_period_id', $fee->academic_period_id) == $period->id) ? 'selected' : '' }}>
                                    {{ $period->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_period_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="class_id" class="form-label">Class <span class="text-danger">*</span></label>
                        <select name="class_id" id="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ (old('class_id', $fee->class_id) == $class->id) ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4 col-md-6">
                        <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="amount" id="amount" 
                                class="form-control @error('amount') is-invalid @enderror" 
                                placeholder="0.00" value="{{ old('amount', $fee->amount) }}" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text text-muted">Enter the total amount to be charged for this specific period.</div>
                    </div>
                </div>

                <div class="mt-4 border-top pt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i data-lucide="refresh-cw" class="me-1" style="width: 18px;"></i> Update Fee Structure
                    </button>
                    <a href="{{ route('admin.fee-management.fees.index') }}" class="btn btn-light ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="col-md-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h6 class="card-title d-flex align-items-center">
                <i data-lucide="info" class="me-2 text-info" style="width: 18px;"></i> Editing Note
            </h6>
            <p class="text-muted small">
                You are currently modifying an existing fee structure. Changes will apply to all students assigned to this category and class for the selected period.
            </p>
            <hr>
            <div class="p-3 rounded">
                <p class="text-muted small mb-1"><strong>Created At:</strong></p>
                <p class="small mb-2">{{ $fee->created_at->format('M d, Y h:i A') }}</p>
                
                <p class="text-muted small mb-1"><strong>Last Updated:</strong></p>
                <p class="small mb-0">{{ $fee->updated_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>
    </div>
</div>


</div>

@endsection