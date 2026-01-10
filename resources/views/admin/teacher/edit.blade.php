@extends('layouts.app')

@section('title', 'Edit Teacher')

@section('content')

<div class="card mb-4 p-4">
<h6 class="d-flex align-items-center mb-0 text-uppercase fw-bold">
<span class="text-muted">Teachers</span>
<i data-lucide="chevron-right" class="mx-2" style="width: 16px;"></i>
<span>Edit Teacher</span>
<i data-lucide="chevron-right" class="mx-2" style="width: 16px;"></i>
<span class="text-primary">{{ $teacher->first_name }} {{ $teacher->last_name }}</span>
</h6>
</div>

@include('includes.message')

<div class="row">
<div class="col-md-12">
<form action="{{ route('admin.teachers.update', $teacher->id) }}" method="POST">
@csrf
@method('PUT')

        <div class="row">
            <!-- Left Column: Personal Information -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title mb-4">Personal Information</h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $teacher->first_name) }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $teacher->last_name) }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $teacher->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $teacher->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Residential Address</label>
                            <textarea name="address" id="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $teacher->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Settings & Staff ID -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title mb-4">Staff Details</h6>
                        
                        <div class="mb-3">
                            <label for="staff_id" class="form-label">Staff ID / Employment Number</label>
                            <input type="text" name="staff_id" id="staff_id" class="form-control @error('staff_id') is-invalid @enderror" value="{{ old('staff_id', $teacher->staff_id) }}" placeholder="TCH-001">
                            @error('staff_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="active" {{ old('status', $teacher->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $teacher->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <hr>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i data-lucide="check-circle" class="me-1" style="width: 18px;"></i> Update Teacher
                            </button>
                            <a href="{{ route('admin.teachers.index') }}" class="btn btn-light">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card border-0">
                    <div class="card-body">
                        <h6 class="card-title d-flex align-items-center">
                            <i data-lucide="info" class="me-2 text-info" style="width: 18px;"></i> Info
                        </h6>
                        <p class="text-muted small mb-0">
                            Updating teacher details will affect their profile information throughout the system. 
                            <strong>Last modified:</strong> {{ $teacher->updated_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>


</div>

@endsection