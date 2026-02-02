@extends('layouts.app')

@section('title', 'Edit Subject - ' . $subject->name)

@section('content')

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <!-- Breadcrumb / Header -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h5 class="mb-0 fw-bold">
                    <span class="text-muted fw-normal">Subjects /</span> Edit Subject
                </h5>
                <a href="{{ route('admin.academics.subjects.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center">
                    <i data-lucide="arrow-left" class="me-2" style="width: 16px;"></i>
                    Back to List
                </a>
            </div>

            @include('includes.message')

            <div class="card border-0 shadow-sm">
                <div class="card-header py-3 border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-primary p-2 rounded me-3">
                            <i data-lucide="edit" class="text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Update Subject Information</h6>
                            <small class="text-muted">Modify the details for subject code: <strong>{{ $subject->code }}</strong></small>
                        </div>
                    </div>
                </div>
                
                <form action="{{ route('admin.academics.subjects.update', $subject->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body p-4">
                        <!-- Subject Name -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase text-muted">Subject Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" 
                                   class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $subject->name) }}" 
                                   placeholder="e.g. Advanced Mathematics" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Subject Code -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase text-muted">Subject Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" 
                                   class="form-control form-control-lg @error('code') is-invalid @enderror" 
                                   value="{{ old('code', $subject->code) }}" 
                                   placeholder="e.g. MATH402" required>
                            <div class="form-text mt-2">The unique identifier used for grading and schedules.</div>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Subject Type -->
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-uppercase text-muted d-block">Subject Type <span class="text-danger">*</span></label>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="form-check custom-radio-card p-3 border rounded @if(old('type', $subject->type) == 'core') border-primary bg-soft-primary @endif">
                                        <input class="form-check-input" type="radio" name="type" id="typeCore" value="core" 
                                            {{ old('type', $subject->type) == 'core' ? 'checked' : '' }}>
                                        <label class="form-check-label d-flex align-items-center cursor-pointer" for="typeCore">
                                            <i data-lucide="file-text" class="me-2 text-info" style="width: 18px;"></i>
                                            Core
                                        </label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check custom-radio-card p-3 border rounded @if(old('type', $subject->type) == 'elective') border-success bg-soft-success @endif">
                                        <input class="form-check-input" type="radio" name="type" id="typeElective" value="elective"
                                            {{ old('type', $subject->type) == 'elective' ? 'checked' : '' }}>
                                        <label class="form-check-label d-flex align-items-center cursor-pointer" for="typeElective">
                                            <i data-lucide="flask-conical" class="me-2 text-success" style="width: 18px;"></i>
                                            Elective
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer  p-4 border-0 text-end">
                        <button type="reset" class="btn btn-link text-muted text-decoration-none me-3">Discard Changes</button>
                        <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">
                            <i data-lucide="save" class="me-2" style="width: 18px;"></i>
                            Update Subject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary { background-color: rgba(13, 110, 253, 0.08); }
    .bg-soft-success { background-color: rgba(25, 135, 84, 0.08); }
    
    .cursor-pointer { cursor: pointer; }
    
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
    }
    
    /* Enhancing the radio selection cards */
    .custom-radio-card {
        transition: all 0.2s ease;
    }
    
    .custom-radio-card:hover {
        border-color: #0d6efd;
        background-color: #0d6efd;
    }
</style>

<script>
    // Visual feedback for radio button selection
    document.querySelectorAll('input[name="type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.custom-radio-card').forEach(card => {
                card.classList.remove('border-primary', 'bg-soft-primary', 'border-success', 'bg-soft-success');
            });
            
            const parent = this.closest('.custom-radio-card');
            if(this.value === 'core') {
                parent.classList.add('border-primary', 'bg-soft-primary');
            } else {
                parent.classList.add('border-success', 'bg-soft-success');
            }
        });
    });
</script>

@endsection