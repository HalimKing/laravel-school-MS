@extends('layouts.app')

@section('title', 'Add Fee Category')

@section('content')

<div class="card mb-4 p-4">
<h6 class="d-flex align-items-center mb-0 text-uppercase fw-bold">
<span class="text-muted">Fee Categories</span>
<i data-lucide="chevron-right" class="mx-2" style="width: 16px;"></i>
<span>Add Category</span>
</h6>
</div>

@include('includes.message')

<div class="row">
<div class="col-md-7 grid-margin stretch-card">
<div class="card border-0 shadow-sm">
<div class="card-body">
<div class="d-flex justify-content-between align-items-center mb-4">
<h6 class="card-title mb-0 text-primary">Category Details</h6>
<a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.fee-management.fee-categories.index') }}">
<i data-lucide="arrow-left" class="me-1" style="width: 14px;"></i> Back to List
</a>
</div>

            <form action="{{ route('admin.fee-management.fee-categories.store') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="name" class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" 
                           class="form-control @error('name') is-invalid @enderror" 
                           placeholder="e.g. Tuition Fees, Sports Levy, Laboratory" 
                           value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label fw-semibold">Description</label>
                    <textarea name="description" id="description" rows="4" 
                              class="form-control @error('description') is-invalid @enderror" 
                              placeholder="Describe what this fee covers...">{{ old('description') }}</textarea>
                    <div class="form-text">Briefly explain the purpose of this fee category for administrative reference.</div>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4 opacity-50">

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.fee-management.fee-categories.index') }}" class="btn btn-light me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i data-lucide="save" class="me-1" style="width: 18px;"></i> Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="col-md-5">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h6 class="card-title d-flex align-items-center mb-3">
                <i data-lucide="help-circle" class="me-2 text-info" style="width: 18px;"></i>
                About Fee Categories
            </h6>
            <p class="text-muted small">
                Fee categories are the broad groups used to classify income. Once created, you will be able to:
            </p>
            <ul class="text-muted small ps-3">
                <li class="mb-2">Define specific amounts for different classes under these categories.</li>
                <li class="mb-2">Generate separate financial reports based on category.</li>
                <li>Set up recurring billing cycles for specific categories.</li>
            </ul>
            <div class="mt-4 p-3  rounded border-start border-4 border-info">
                <p class="mb-0 small fw-bold text-dark">Common Examples:</p>
                <p class="mb-0 small text-muted italic">Tuition, Transport, Uniforms, Library, Examination, and PTA Levies.</p>
            </div>
        </div>
    </div>
</div>


</div>

@endsection