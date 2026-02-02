@extends('layouts.app')

@section('title', 'Edit Subject Assignment')

@section('content')
<div class="card mb-4 p-4">
    <div class="d-flex align-items-center">
        <a href="{{ route('admin.academics.assign-subjects.index') }}" class="btn btn-outline-secondary btn-sm me-3">
            <i data-lucide="arrow-left" style="width: 16px;"></i>
        </a>
        <h6 class="mb-0 text-uppercase fw-bold">Edit Subject Assignment</h6>
    </div>
</div>

@include('includes.message')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title mb-4">Assignment Information</h6>
                
                <form method="POST" action="{{ route('admin.academics.assign-subjects.update', $subjectAssignment->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="teacher_id" class="form-label">Select Teacher <span class="text-danger">*</span></label>
                            <select class="form-select @error('teacher_id') is-invalid @enderror" id="teacher_id" name="teacher_id" required>
                                <option value="">Choose a teacher...</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ (old('teacher_id', $subjectAssignment->teacher_id) == $teacher->id) ? 'selected' : '' }}>
                                        {{ $teacher->first_name }} {{ $teacher->last_name }} - {{ $teacher->specialization ?? 'General' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('teacher_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                            <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id" required>
                                <option value="">Choose a subject...</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ (old('subject_id', $subjectAssignment->subject_id) == $subject->id) ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="class_id" class="form-label">Class Level <span class="text-danger">*</span></label>
                            <select class="form-select @error('class_id') is-invalid @enderror" id="class_id" name="class_id" required>
                                <option value="">Choose a class...</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ (old('class_id', $subjectAssignment->class_id) == $class->id) ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="academic_year_id" class="form-label">Academic Year <span class="text-danger">*</span></label>
                            <select class="form-select @error('academic_year_id') is-invalid @enderror" id="academic_year_id" name="academic_year_id" required>
                                <option value="">Year...</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ (old('academic_year_id', $subjectAssignment->academic_year_id) == $year->id) ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_year_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ (old('status', $subjectAssignment->status) == 'active') ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ (old('status', $subjectAssignment->status) == 'inactive') ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save" class="me-1" style="width: 18px;"></i> Update Assignment
                        </button>
                        <a href="{{ route('admin.academics.assign-subjects.index') }}" class="btn btn-outline-secondary">
                            <i data-lucide="x" class="me-1" style="width: 18px;"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
@endpush