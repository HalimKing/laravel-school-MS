@extends('layouts.app')

@section('title', 'Subject Assignment')

@section('content')
<div class="card mb-4 p-4">
    <h6 class="mb-0 text-uppercase fw-bold">Subject Assignment</h6>
</div>

@include('includes.message')

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded-circle bg-primary-subtle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i data-lucide="book" class="text-primary" style="width: 24px; height: 24px;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h2 class="mb-0 fw-bold">{{ $totalSubjects ?? 24 }}</h2>
                        <p class="text-muted mb-0 small">Total Subjects</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded-circle bg-success-subtle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i data-lucide="user-check" class="text-success" style="width: 24px; height: 24px;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h2 class="mb-0 fw-bold">{{ $activeAssignments ?? 48 }}</h2>
                        <p class="text-muted mb-0 small">Active Assignments</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded-circle bg-warning-subtle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i data-lucide="users" class="text-warning" style="width: 24px; height: 24px;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h2 class="mb-0 fw-bold">{{ $totalTeachers ?? 18 }}</h2>
                        <p class="text-muted mb-0 small">Total Teachers</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded-circle bg-info-subtle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i data-lucide="layers" class="text-info" style="width: 24px; height: 24px;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h2 class="mb-0 fw-bold">{{ $totalClasses ?? 12 }}</h2>
                        <p class="text-muted mb-0 small">Class Levels</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assignment Form -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <i data-lucide="plus-circle" class="me-2 text-primary" style="width: 20px; height: 20px;"></i>
                    <h6 class="card-title mb-0">New Subject Assignment</h6>
                </div>
                
                <form method="POST" action="{{ route('admin.academics.assign-subjects.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="teacher_id" class="form-label">Select Teacher <span class="text-danger">*</span></label>
                            <select class="form-select @error('teacher_id') is-invalid @enderror" id="teacher_id" name="teacher_id" required>
                                <option value="">Choose a teacher...</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->first_name }} {{ $teacher->last_name }} - {{ $teacher->specialization ?? 'General' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('teacher_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                            <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id" required>
                                <option value="">Choose a subject...</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="class_id" class="form-label">Class Level <span class="text-danger">*</span></label>
                            <select class="form-select @error('class_id') is-invalid @enderror" id="class_id" name="class_id" required>
                                <option value="">Choose a class...</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-2 mb-3">
                            <label for="academic_year_id" class="form-label">Academic Year <span class="text-danger">*</span></label>
                            <select class="form-select @error('academic_year_id') is-invalid @enderror" id="academic_year_id" name="academic_year_id" required>
                                <option value="">Year...</option>
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
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="plus-circle" class="me-1" style="width: 18px;"></i> Add Assignment
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i data-lucide="refresh-cw" class="me-1" style="width: 18px;"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.academics.assign-subjects.index') }}">
                    <div class="row align-items-end">
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label for="filter_class" class="form-label">Filter by Class</label>
                            <select class="form-select" id="filter_class" name="class_id">
                                <option value="">All Classes</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mb-2 mb-md-0">
                            <label for="filter_subject" class="form-label">Filter by Subject</label>
                            <select class="form-select" id="filter_subject" name="subject_id">
                                <option value="">All Subjects</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-2 mb-md-0">
                            <label for="search" class="form-label">Search Teacher</label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="Search teacher name..." value="{{ request('search') }}">
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i data-lucide="search" class="me-1" style="width: 18px;"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Assignments Table -->
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title mb-0">Current Subject Assignments</h6>
                    <span class="badge bg-primary">{{ $assignments->total() ?? 0 }} Total</span>
                </div>
                
                <div class="table-responsive">
                    <table id="dataTableExample" class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Teacher Name</th>
                                <th>Subject</th>
                                <th>Class Level</th>
                                <th>Academic Year</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignments as $assignment)
                                <tr>
                                    <td class="align-middle">
                                        <span class="text-muted">#{{ str_pad($assignment->id, 3, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2 fw-semibold" style="width: 36px; height: 36px; font-size: 14px;">
                                                {{ strtoupper(substr($assignment->teacher->first_name, 0, 1) . substr($assignment->teacher->last_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $assignment->teacher->first_name }} {{ $assignment->teacher->last_name }}</div>
                                                <small class="text-muted">{{ $assignment->teacher->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="fw-medium">{{ $assignment->subject->name }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge bg-info-subtle text-info">{{ $assignment->class->name }}</span>
                                    </td>
                                    <td class="align-middle">
                                        {{ $assignment->academicYear->name ?? 'N/A' }}
                                    </td>
                                    <td class="align-middle">
                                        @if($assignment->status == 'active')
                                            <span class="badge bg-success-subtle text-success">Active</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end align-middle">
                                        <div class="dropdown">
                                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.academics.assign-subjects.edit', $assignment->id) }}">
                                                        <i data-lucide="pencil" class="me-2 text-muted" style="width: 14px;"></i> Edit
                                                    </a>
                                                </li>
                                                {{-- <li>
                                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.academics.assign-subjects.show', $assignment->id) }}">
                                                        <i data-lucide="eye" class="me-2 text-muted" style="width: 14px;"></i> View Details
                                                    </a>
                                                </li> --}}
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form method="POST" action="{{ route('admin.academics.assign-subjects.destroy', $assignment->id) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger d-flex align-items-center" onclick="return confirm('Delete this subject assignment permanently?')">
                                                            <i data-lucide="trash-2" class="me-2" style="width: 14px;"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i data-lucide="clipboard" class="mb-2" style="width: 40px; height: 40px; opacity: 0.2;"></i>
                                        <p class="mb-0">No subject assignments found.</p>
                                        <small>Create your first assignment using the form above.</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($assignments->hasPages())
                    <div class="mt-4 d-flex justify-content-end w-4">
                        {{ $assignments->links() }}
                    </div>
                @endif
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

    // Auto-dismiss success messages
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
@endpush