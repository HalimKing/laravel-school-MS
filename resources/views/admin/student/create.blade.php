@extends('layouts.app')

@section('title', 'Add New Student')

@section('content')

<div class="card mb-4 p-4">
<h6 class="d-flex align-items-center mb-0 text-uppercase fw-bold">
<span class="text-muted">Students</span>
<i data-lucide="chevron-right" class="mx-2" style="width: 16px;"></i>
<span>Add Student</span>
</h6>
</div>

@include('includes.message')

<form action="{{ route('admin.students.store') }}" method="POST">
@csrf
<div class="row">
<!-- Main Form Column -->
<div class="col-md-8">
<!-- Personal Information -->
<div class="card mb-4">
<div class="card-body">
<h6 class="card-title mb-4 d-flex align-items-center">
<i data-lucide="user" class="me-2 text-primary" style="width: 18px;"></i>
Personal Information
</h6>
<div class="row">
<div class="col-md-6 mb-3">
<label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
<input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
@error('first_name')
<div class="invalid-feedback">{{ $message }}</div>
@enderror
</div>
<div class="col-md-6 mb-3">
<label for="middle_name" class="form-label">Middle Name</label>
<input type="text" name="middle_name" id="middle_name" class="form-control @error('middle_name') is-invalid @enderror" value="{{ old('middle_name') }}">
@error('middle_name')
<div class="invalid-feedback">{{ $message }}</div>
@enderror
</div>
</div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="other_name" class="form-label">Other Name</label>
                        <input type="text" name="other_name" id="other_name" class="form-control @error('other_name') is-invalid @enderror" value="{{ old('other_name') }}">
                        @error('other_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                        <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror" required>
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth') }}">
                        @error('date_of_birth')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-0">
                    <label for="address" class="form-label">Residential Address</label>
                    <textarea name="address" id="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Parent Information -->
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="card-title mb-4 d-flex align-items-center">
                    <i data-lucide="users" class="me-2 text-primary" style="width: 18px;"></i>
                    Parent Details
                </h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="parent_name" class="form-label">Parent Name</label>
                        <input type="text" name="parent_name" id="parent_name" class="form-control @error('parent_name') is-invalid @enderror" value="{{ old('parent_name') }}">
                        @error('parent_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="parent_phone" class="form-label">Parent Phone</label>
                        <input type="text" name="parent_phone" id="parent_phone" class="form-control @error('parent_phone') is-invalid @enderror" value="{{ old('parent_phone') }}">
                        @error('parent_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="parent_email" class="form-label">Parent Email</label>
                        <input type="email" name="parent_email" id="parent_email" class="form-control @error('parent_email') is-invalid @enderror" value="{{ old('parent_email') }}">
                        @error('parent_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Column -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="card-title mb-4 d-flex align-items-center">
                    <i data-lucide="book-open" class="me-2 text-primary" style="width: 18px;"></i>
                    Academic Details
                </h6>
                
                <div class="mb-3">
                    <label for="student_id" class="form-label">Student ID <span class="text-danger">*</span></label>
                    <input type="text" name="student_id" id="student_id" class="form-control @error('student_id') is-invalid @enderror" value="{{ old('student_id') }}" required>
                    @error('student_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="academic_year_id" class="form-label">Academic Year <span class="text-danger">*</span></label>
                    <select name="academic_year_id" id="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                        <option value="">Select Year</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                        @endforeach
                    </select>
                    @error('academic_year_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="class_id" class="form-label">Assign Class <span class="text-danger">*</span></label>
                    <select name="class_id" id="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                    @error('class_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <hr>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="save" class="me-1" style="width: 18px;"></i> Save Student
                    </button>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </div>

        <div class="card border-0">
            <div class="card-body">
                <h6 class="card-title d-flex align-items-center">
                    <i data-lucide="info" class="me-2 text-info" style="width: 18px;"></i> Quick Note
                </h6>
                <p class="text-muted small mb-0">
                    Ensure the Student ID is unique. This number will be used for all academic tracking and reporting for this student.
                </p>
            </div>
        </div>
    </div>
</div>


</form>

@endsection