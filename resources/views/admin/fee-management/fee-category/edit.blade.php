@extends('layouts.app')

@section('title', 'Edit Fee Category')

@section('content')

<div class="card mb-4 p-4">
<h6 class="d-flex align-items-center mb-0 text-uppercase fw-bold">
<span class="text-muted">Fee Categories</span>
<i data-lucide="chevron-right" class="mx-2" style="width: 16px;"></i>
<span>Edit Category</span>
<i data-lucide="chevron-right" class="mx-2" style="width: 16px;"></i>
<span class="text-primary">{{ $feeCategory->name }}</span>
</h6>
</div>

@include('includes.message')

<div class="row">
<div class="col-md-7 grid-margin stretch-card">
<div class="card border-0 shadow-sm">
<div class="card-body">
<div class="d-flex justify-content-between align-items-center mb-4">
<h6 class="card-title mb-0 text-primary">Modify Category Details</h6>
<a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.fee-management.fee-categories.index') }}">
<i data-lucide="arrow-left" class="me-1" style="width: 14px;"></i> Back to List
</a>
</div>

            <form action="{{ route('admin.fee-management.fee-categories.update', $feeCategory->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label for="name" class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" 
                           class="form-control @error('name') is-invalid @enderror" 
                           placeholder="e.g. Tuition Fees, Sports Levy, Laboratory" 
                           value="{{ old('name', $feeCategory->name) }}" required autofocus>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label fw-semibold">Description</label>
                    <textarea name="description" id="description" rows="4" 
                              class="form-control @error('description') is-invalid @enderror" 
                              placeholder="Describe what this fee covers...">{{ old('description', $feeCategory->description) }}</textarea>
                    <div class="form-text">Briefly explain the purpose of this fee category for administrative reference.</div>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4 opacity-50">

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.fee-management.fee-categories.index') }}" class="btn btn-light me-2">Cancel</a>
                    <button type="submit" class="btn btn-success px-4">
                        <i data-lucide="check-circle" class="me-1" style="width: 18px;"></i> Update Category
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
                <i data-lucide="info" class="me-2 text-info" style="width: 18px;"></i>
                Editing Information
            </h6>
            <p class="text-muted small">
                You are currently modifying the <strong>{{ $feeCategory->name }}</strong> category.
            </p>
            <p class="text-muted small">
                Please note that changing the name of this category will update it across all linked fee structures and financial reports.
            </p>
            <div class="mt-4 p-3 rounded border-start border-4 border-warning">
                <p class="mb-0 small fw-bold text-dark">Audit Trail:</p>
                <p class="mb-0 small text-muted">Created: {{ $feeCategory->created_at->format('M d, Y') }}</p>
                <p class="mb-0 small text-muted">Last Update: {{ $feeCategory->updated_at->diffForHumans() }}</p>
            </div>
        </div>
    </div>
</div>


</div>

@endsection