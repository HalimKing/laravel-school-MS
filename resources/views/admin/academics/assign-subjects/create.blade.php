@extends('layouts.app')

@section('title', 'Assign Subjects')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Assign Subjects to Teachers</h1>
            <p class="text-muted small mb-0">Select multiple teachers, grade levels, and subjects to create bulk assignments.</p>
        </div>
        <a href="{{ route('admin.academics.assign-subjects.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>
            Back to List
        </a>
    </div>

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Whoops!</strong> There were some problems with your input.
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Success Message -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.academics.assign-subjects.store') }}" method="POST" id="assignmentForm">
        @csrf
        
        <div class="row g-4">
            <!-- Teachers Selection -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-person-check me-2"></i>
                            Teacher
                        </h5>
                        <span class="badge bg-white text-primary" id="teacher-count">None selected</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-3">
                            <input type="text" class="form-control form-control-sm" id="search-teachers" placeholder="Search teachers...">
                        </div>
                        <div class="alert alert-info py-2 px-3 small mb-2" role="alert">
                            <i class="bi bi-info-circle me-1"></i>
                            Select one teacher to assign
                        </div>
                        <hr class="my-2">
                        <div id="teachers-list" class="overflow-auto" style="max-height: 400px;">
                            @foreach($teachers as $teacher)
                                <div class="form-check mb-2 teacher-item">
                                    <input class="form-check-input teacher-radio" type="radio" name="teacher_id" value="{{ $teacher->id }}" id="teacher-{{ $teacher->id }}">
                                    <label class="form-check-label" for="teacher-{{ $teacher->id }}">
                                        {{ $teacher->first_name }} {{ $teacher->last_name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grade Levels Selection -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-mortarboard me-2"></i>
                            Grade Levels
                        </h5>
                        <span class="badge bg-white text-success" id="grade-count">0 selected</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-3">
                            <input type="text" class="form-control form-control-sm" id="search-grades" placeholder="Search grade levels...">
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="select-all-grades">
                            <label class="form-check-label fw-semibold text-success" for="select-all-grades">
                                Select All Grade Levels
                            </label>
                        </div>
                        <hr class="my-2">
                        <div id="grades-list" class="overflow-auto" style="max-height: 400px;">
                            @foreach($classes as $class)
                                <div class="form-check mb-2 grade-item">
                                    <input class="form-check-input grade-checkbox" type="checkbox" name="grade_levels[]" value="{{ $class->id }}" id="grade-{{ $class->id }}">
                                    <label class="form-check-label" for="grade-{{ $class->id }}">
                                        {{ $class->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subjects Selection -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-book me-2"></i>
                            Subjects
                        </h5>
                        <span class="badge bg-white text-info" id="subject-count">0 selected</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-3">
                            <input type="text" class="form-control form-control-sm" id="search-subjects" placeholder="Search subjects...">
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="select-all-subjects">
                            <label class="form-check-label fw-semibold text-info" for="select-all-subjects">
                                Select All Subjects
                            </label>
                        </div>
                        <hr class="my-2">
                        <div id="subjects-list" class="overflow-auto" style="max-height: 400px;">
                            @foreach($subjects as $subject)
                                <div class="form-check mb-2 subject-item">
                                    <input class="form-check-input subject-checkbox" type="checkbox" name="subjects[]" value="{{ $subject->id }}" id="subject-{{ $subject->id }}">
                                    <label class="form-check-label" for="subject-{{ $subject->id }}">
                                        {{ $subject->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignment Summary -->
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="bi bi-clipboard-data me-2"></i>
                    Assignment Summary
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="p-3 bg-primary bg-opacity-10 rounded">
                            <h2 class="display-6 text-primary mb-0" id="summary-teachers">0</h2>
                            <p class="text-muted small mb-0">Teacher Selected</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="p-3 bg-success bg-opacity-10 rounded">
                            <h2 class="display-6 text-success mb-0" id="summary-grades">0</h2>
                            <p class="text-muted small mb-0">Grade Levels Selected</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-info bg-opacity-10 rounded">
                            <h2 class="display-6 text-info mb-0" id="summary-subjects">0</h2>
                            <p class="text-muted small mb-0">Subjects Selected</p>
                        </div>
                    </div>
                </div>
                <hr class="my-3">
                <div class="alert alert-warning mb-0" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Total Assignments to Create: <span id="total-assignments" class="fw-bold">0</span></strong>
                    <p class="mb-0 small mt-2">This will create an assignment for the selected teacher with each combination of grade level and subject.</p>
                </div>
            </div>
            <div class="card-footer bg-light border-top d-flex justify-content-between align-items-center py-3">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="clear-all">
                    <i class="bi bi-x-circle me-1"></i>
                    Clear All Selections
                </button>
                <div class="d-flex gap-2">
                    <button type="reset" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Reset
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm px-4" id="submit-btn" disabled>
                        <i class="bi bi-save me-1"></i>
                        Create Assignments
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .form-check-label {
        cursor: pointer;
        user-select: none;
    }

    .form-check:hover {
        background-color: #f8f9fa;
        border-radius: 4px;
    }

    .overflow-auto::-webkit-scrollbar {
        width: 8px;
    }

    .overflow-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .overflow-auto::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    .overflow-auto::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .card-header h5 {
        font-size: 1.1rem;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get all checkboxes and radio buttons
        const teacherRadios = document.querySelectorAll('.teacher-radio');
        const gradeCheckboxes = document.querySelectorAll('.grade-checkbox');
        const subjectCheckboxes = document.querySelectorAll('.subject-checkbox');

        // Get select all checkboxes (only for grades and subjects now)
        const selectAllGrades = document.getElementById('select-all-grades');
        const selectAllSubjects = document.getElementById('select-all-subjects');

        // Get counters
        const teacherCount = document.getElementById('teacher-count');
        const gradeCount = document.getElementById('grade-count');
        const subjectCount = document.getElementById('subject-count');
        const summaryTeachers = document.getElementById('summary-teachers');
        const summaryGrades = document.getElementById('summary-grades');
        const summarySubjects = document.getElementById('summary-subjects');
        const totalAssignments = document.getElementById('total-assignments');
        const submitBtn = document.getElementById('submit-btn');

        // Search inputs
        const searchTeachers = document.getElementById('search-teachers');
        const searchGrades = document.getElementById('search-grades');
        const searchSubjects = document.getElementById('search-subjects');

        // Update counts and totals
        function updateCounts() {
            const teacherSelected = document.querySelector('.teacher-radio:checked') ? 1 : 0;
            const gradesSelected = document.querySelectorAll('.grade-checkbox:checked').length;
            const subjectsSelected = document.querySelectorAll('.subject-checkbox:checked').length;

            // Update badges
            if (teacherSelected === 1) {
                const selectedTeacher = document.querySelector('.teacher-radio:checked');
                const teacherName = selectedTeacher.nextElementSibling.textContent.trim();
                teacherCount.textContent = teacherName;
            } else {
                teacherCount.textContent = 'None selected';
            }
            
            gradeCount.textContent = gradesSelected + ' selected';
            subjectCount.textContent = subjectsSelected + ' selected';

            // Update summary
            summaryTeachers.textContent = teacherSelected;
            summaryGrades.textContent = gradesSelected;
            summarySubjects.textContent = subjectsSelected;

            // Calculate total combinations (1 teacher × grades × subjects)
            const total = teacherSelected * gradesSelected * subjectsSelected;
            totalAssignments.textContent = total;

            // Enable/disable submit button
            submitBtn.disabled = total === 0;

            // Update select all checkboxes state
            selectAllGrades.checked = gradesSelected === gradeCheckboxes.length && gradesSelected > 0;
            selectAllSubjects.checked = subjectsSelected === subjectCheckboxes.length && subjectsSelected > 0;
        }

        // Select all functionality for grades
        selectAllGrades.addEventListener('change', function() {
            gradeCheckboxes.forEach(checkbox => {
                if (checkbox.closest('.grade-item').style.display !== 'none') {
                    checkbox.checked = this.checked;
                }
            });
            updateCounts();
        });

        // Select all functionality for subjects
        selectAllSubjects.addEventListener('change', function() {
            subjectCheckboxes.forEach(checkbox => {
                if (checkbox.closest('.subject-item').style.display !== 'none') {
                    checkbox.checked = this.checked;
                }
            });
            updateCounts();
        });

        // Add change listeners to teacher radio buttons
        teacherRadios.forEach(radio => {
            radio.addEventListener('change', updateCounts);
        });

        // Add change listeners to grade checkboxes
        gradeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateCounts);
        });

        // Add change listeners to subject checkboxes
        subjectCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateCounts);
        });

        // Search functionality
        searchTeachers.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            document.querySelectorAll('.teacher-item').forEach(item => {
                const label = item.querySelector('label').textContent.toLowerCase();
                item.style.display = label.includes(searchTerm) ? 'block' : 'none';
            });
        });

        searchGrades.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            document.querySelectorAll('.grade-item').forEach(item => {
                const label = item.querySelector('label').textContent.toLowerCase();
                item.style.display = label.includes(searchTerm) ? 'block' : 'none';
            });
        });

        searchSubjects.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            document.querySelectorAll('.subject-item').forEach(item => {
                const label = item.querySelector('label').textContent.toLowerCase();
                item.style.display = label.includes(searchTerm) ? 'block' : 'none';
            });
        });

        // Clear all selections
        document.getElementById('clear-all').addEventListener('click', function() {
            teacherRadios.forEach(radio => radio.checked = false);
            gradeCheckboxes.forEach(cb => cb.checked = false);
            subjectCheckboxes.forEach(cb => cb.checked = false);
            selectAllGrades.checked = false;
            selectAllSubjects.checked = false;
            updateCounts();
        });

        // Form submission validation
        document.getElementById('assignmentForm').addEventListener('submit', function(e) {
            const total = parseInt(totalAssignments.textContent);
            
            if (total === 0) {
                e.preventDefault();
                alert('Please select one teacher, at least one grade level, and at least one subject.');
                return false;
            }

            const teacherSelected = document.querySelector('.teacher-radio:checked');
            if (!teacherSelected) {
                e.preventDefault();
                alert('Please select a teacher.');
                return false;
            }

            if (total > 100) {
                if (!confirm(`You are about to create ${total} assignments. This may take a while. Do you want to continue?`)) {
                    e.preventDefault();
                    return false;
                }
            }
        });

        // Initialize counts
        updateCounts();
    });
</script>
@endsection