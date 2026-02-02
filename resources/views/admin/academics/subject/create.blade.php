@extends('layouts.app')

@section('title', 'Add New Subject')

@section('content')

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <!-- Header Section -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="mb-1 fw-bold">Create New Subject</h4>
                    <p class="text-muted mb-0">Add a new academic course to the system.</p>
                </div>
                <a href="{{ route('admin.academics.subjects.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center">
                    <i data-lucide="list" class="me-2" style="width: 16px;"></i>
                    View All
                </a>
            </div>

            @include('includes.message')

            <div class="card border-0 shadow-sm overflow-hidden">
                <!-- Decorative Top Border -->
                <div style="height: 4px;" class="bg-primary"></div>
                
                <form action="{{ route('admin.academics.subjects.store') }}" method="POST">
                    @csrf
                    
                    <div class="card-body p-4">
                        <!-- Subject Name -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase text-muted">Subject Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i data-lucide="book-open" class="text-muted" style="width: 18px;"></i>
                                </span>
                                <input type="text" name="name" 
                                       class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" 
                                       placeholder="e.g. Database Management Systems" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Subject Code -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase text-muted">Subject Code <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i data-lucide="hash" class="text-muted" style="width: 18px;"></i>
                                </span>
                                <input type="text" name="code" 
                                       class="form-control border-start-0 ps-0 @error('code') is-invalid @enderror" 
                                       value="{{ old('code') }}" 
                                       placeholder="e.g. CS102" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted mt-2 d-block">Ensure this code is unique across the department.</small>
                        </div>

                        <!-- Subject Type -->
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-uppercase text-muted d-block mb-3">Teaching Format <span class="text-danger">*</span></label>
                            <div class="row g-3">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="type" id="typeTheory" value="theory" 
                                        {{ old('type', 'theory') == 'theory' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary w-100 p-3 text-start radio-card" for="typeTheory">
                                        <div class="d-flex flex-column">
                                            <i data-lucide="presentation" class="mb-2" style="width: 24px; height: 24px;"></i>
                                            <span class="fw-bold">Theory</span>
                                            <small class="opacity-75">Classroom based learning</small>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="type" id="typePractical" value="practical"
                                        {{ old('type') == 'practical' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-success w-100 p-3 text-start radio-card" for="typePractical">
                                        <div class="d-flex flex-column">
                                            <i data-lucide="beaker" class="mb-2" style="width: 24px; height: 24px;"></i>
                                            <span class="fw-bold">Practical</span>
                                            <small class="opacity-75">Lab or field based session</small>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            @error('type')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer bg-light p-4 border-0 d-flex justify-content-between align-items-center">
                        <button type="reset" class="btn btn-link text-muted text-decoration-none">Clear Form</button>
                        <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm rounded-pill">
                            Create Subject
                            <i data-lucide="chevron-right" class="ms-2" style="width: 18px;"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling for the custom radio button cards */
    .radio-card {
        border-width: 2px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background-color: #fff;
    }

    .btn-check:checked + .radio-card {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .btn-check:checked + .btn-outline-primary {
        background-color: rgba(13, 110, 253, 0.05);
    }

    .btn-check:checked + .btn-outline-success {
        background-color: rgba(25, 135, 84, 0.05);
    }

    .form-control:focus {
        box-shadow: none;
        border-color: #dee2e6;
    }

    .input-group:focus-within {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        border-radius: 0.375rem;
    }

    .input-group:focus-within .input-group-text,
    .input-group:focus-within .form-control {
        border-color: #0d6efd;
    }
</style>

<script>
    // Lucide icons are initialized globally in app.js, 
    // but we can re-run if needed for dynamic content.
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>

@endsection