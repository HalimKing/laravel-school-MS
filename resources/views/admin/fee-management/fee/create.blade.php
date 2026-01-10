@extends('layouts.app')

@section('title', 'Configure Fee Structure')

@section('content')

<div class="card mb-4 p-4">
<h6 class="d-flex align-items-center mb-0 text-uppercase fw-bold">
<span class="text-muted">Fee Management</span>
<i data-lucide="chevron-right" class="mx-2" style="width: 16px;"></i>
<span>Configure New Fee</span>
</h6>
</div>

@include('includes.message')

<div class="row">
<div class="col-md-8 grid-margin stretch-card">
<div class="card border-0 shadow-sm">
<div class="card-body">
<div class="d-flex justify-content-between align-items-center mb-4">
<h6 class="card-title mb-0">Fee Configuration Form</h6>
<a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.fee-management.fees.index') }}">
<i data-lucide="arrow-left" class="me-1" style="width: 14px;"></i> Back to List
</a>
</div>

            <form action="{{ route('admin.fee-management.fees.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="fee_category_id" class="form-label">Fee Category <span class="text-danger">*</span></label>
                    <select name="fee_category_id" id="fee_category_id" class="form-select @error('fee_category_id') is-invalid @enderror" required>
                        <option value="">Select Category</option>
                        @foreach($feeCategories as $category)
                            <option value="{{ $category->id }}" {{ old('fee_category_id') == $category->id ? 'selected' : '' }}>
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
                                <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
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
                                <option value="{{ $period->id }}" {{ old('academic_period_id') == $period->id ? 'selected' : '' }}>
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
                                <option value="{{ $class->id }}" {{ old('class_id') == $year->id ? 'selected' : '' }}>
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
                                placeholder="0.00" value="{{ old('amount') }}" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text text-muted">Enter the total amount to be charged for this specific period.</div>
                    </div>
                </div>
                

                <div class="mt-4 border-top pt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i data-lucide="save" class="me-1" style="width: 18px;"></i> Save Fee Structure
                    </button>
                    <a href="{{ route('admin.fee-management.fees.index') }}" class="btn btn-light ms-2">Cancel</a>
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
                <strong>Fee Categories:</strong> Group fees such as Tuition, Library, or Sports.
            </p>
            <p class="text-muted small">
                <strong>Academic Logic:</strong> Fee structures are tied to specific periods. This allows you to set different amounts for different terms or semesters within the same academic year.
            </p>
            <hr>
            <p class="text-muted small mb-0">
                <i data-lucide="alert-circle" class="me-1 text-warning" style="width: 14px;"></i>
                Ensure you have already created the relevant Academic Year and Period before configuring the fee.
            </p>
        </div>
    </div>
</div>


</div>

@endsection