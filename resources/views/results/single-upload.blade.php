@extends('layouts.app')

@section('title', 'Single Result Upload')

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center">
        <h6 class="mb-0 text-uppercase fw-bold">Record Individual Student Result</h6>
    </div>
</div>

@include('includes.message')

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <h5 class="alert-heading fw-bold mb-3">
        <i data-lucide="alert-triangle" style="width: 18px;" class="me-2"></i>Please Fix the Following Errors:
    </h5>
    <ul class="mb-0">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('results.single-upload.store') }}" method="POST" id="singleResultForm">
                    @csrf

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Academic Year <span class="text-danger">*</span></label>
                            <select name="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" id="academicYear" required>
                                <option value="">Select academic year...</option>
                                @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('academic_year_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Academic Period <span class="text-danger">*</span></label>
                            <select name="academic_period_id" class="form-select @error('academic_period_id') is-invalid @enderror" id="academicPeriod" required>
                                <option value="">Select period...</option>
                                @foreach($academicPeriods as $period)
                                <option value="{{ $period->id }}" {{ old('academic_period_id') == $period->id ? 'selected' : '' }}>
                                    {{ $period->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('academic_period_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" id="subject" required>
                                <option value="">Select subject...</option>
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Class <span class="text-danger">*</span></label>
                            <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" id="class" required>
                                <option value="">Select class...</option>
                                @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('class_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Student <span class="text-danger">*</span></label>
                            <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" id="student" required disabled>
                                <option value="">First select a class...</option>
                            </select>
                            @error('student_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Assessment Scores Section -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold mb-3">Assessment Scores</h6>
                                    <p class="text-muted mb-3">First select subject and class to load assessments</p>
                                    <div id="assessmentScoresContainer">
                                        <!-- Dynamic assessment fields will be loaded here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="reset" class="btn btn-light me-2">Clear</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i data-lucide="save" class="me-2" style="width: 16px;"></i>Record Result
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Duplicate Results Modal -->
<div class="modal fade" id="duplicateModal" tabindex="-1" aria-labelledby="duplicateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="duplicateModalLabel">
                    <i data-lucide="alert-circle" style="width: 20px;" class="me-2"></i>Duplicate Results Detected
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">The following assessment result(s) already exist for this student:</p>
                <ul class="list-group" id="duplicateList">
                </ul>
                <p class="mt-3 text-muted small">To modify existing results, please use the bulk upload feature with the option to update, or delete and re-enter the result.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="proceedBtn">Proceed with New Entries Only</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const classSelect = document.getElementById('class');
        const academicYearSelect = document.getElementById('academicYear');
        const studentSelect = document.getElementById('student');
        const subjectSelect = document.getElementById('subject');
        const form = document.getElementById('singleResultForm');
        const submitBtn = document.getElementById('submitBtn');
        const duplicateModal = new bootstrap.Modal(document.getElementById('duplicateModal'));
        let allowSubmit = false;

        // Function to load students
        function loadStudents() {
            const academicYearId = academicYearSelect.value;
            const classId = classSelect.value;
            if (classId && academicYearId) {
                fetch(`/results/get-students/${academicYearId}/${classId}`)
                    .then(response => response.json())
                    .then(data => {
                        studentSelect.innerHTML = '<option value="">Select student...</option>';
                        data.forEach(student => {
                            const option = document.createElement('option');
                            option.value = student.id;
                            option.textContent = `${student.name} (${student.registration_number})`;
                            studentSelect.appendChild(option);
                        });
                        studentSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        studentSelect.innerHTML = '<option value="">Error loading students</option>';
                    });
            } else {
                studentSelect.innerHTML = '<option value="">First select both academic year and class...</option>';
                studentSelect.disabled = true;
            }
        }

        // Load students when class changes
        classSelect.addEventListener('change', loadStudents);

        // Load students when academic year changes
        academicYearSelect.addEventListener('change', loadStudents);

        // Load assessments when subject and class are selected
        function loadAssessments() {
            const subjectId = subjectSelect.value;
            const classId = classSelect.value;
            const container = document.getElementById('assessmentScoresContainer');

            if (subjectId && classId) {
                fetch(`/results/get-assessments/${subjectId}/${classId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            container.innerHTML = '<div class="alert alert-warning mb-0">No assessments found for this subject and class</div>';
                        } else {
                            let html = '<div class="row">';
                            data.forEach(assessment => {
                                html += `
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">${assessment.name} <span class="text-muted">(${assessment.percentage}%)</span></label>
                                        <div class="input-group">
                                            <input type="number" name="scores[${assessment.id}]" class="form-control" 
                                                placeholder="0" min="0" max="100" step="0.01">
                                            <span class="input-group-text">/100</span>
                                        </div>
                                    </div>
                                `;
                            });
                            html += '</div>';
                            container.innerHTML = html;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        container.innerHTML = '<div class="alert alert-danger mb-0">Error loading assessments</div>';
                    });
            } else {
                container.innerHTML = '<p class="text-muted mb-0">First select subject and class to load assessments</p>';
            }
        }

        subjectSelect.addEventListener('change', loadAssessments);
        classSelect.addEventListener('change', loadAssessments);

        // Check for duplicates before submitting
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // If already approved, submit the form
            if (allowSubmit) {
                allowSubmit = false;
                form.submit();
                return;
            }

            const studentId = studentSelect.value;
            const academicYearId = document.getElementById('academicYear').value;
            const academicPeriodId = document.getElementById('academicPeriod').value;

            // Get assessment IDs with scores entered
            const assessmentIds = [];
            const scoreInputs = document.querySelectorAll('input[name^="scores["]');
            scoreInputs.forEach(input => {
                if (input.value) { // Only include fields with values
                    const assessmentId = input.name.match(/\[(\d+)\]/)[1];
                    assessmentIds.push(assessmentId);
                }
            });

            if (!studentId || assessmentIds.length === 0) {
                alert('Please select a student and enter at least one score.');
                return;
            }

            try {
                const response = await fetch('/results/check-duplicates', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        student_id: studentId,
                        academic_year_id: academicYearId,
                        academic_period_id: academicPeriodId,
                        assessment_ids: assessmentIds
                    })
                });

                const data = await response.json();

                if (data.has_duplicates) {
                    // Show duplicate warning modal
                    const duplicateList = document.getElementById('duplicateList');
                    duplicateList.innerHTML = '';
                    data.duplicates.forEach(dup => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item';
                        li.innerHTML = `
                            <strong>${dup.assessment_name}</strong>
                            <br>
                            <small class="text-muted">Current score: ${dup.existing_score}</small>
                        `;
                        duplicateList.appendChild(li);
                    });

                    duplicateModal.show();
                } else {
                    // No duplicates, submit the form
                    allowSubmit = true;
                    form.submit();
                }
            } catch (error) {
                console.error('Error checking duplicates:', error);
                alert('Error checking for duplicates. Please try again.');
            }
        });

        // Proceed button in modal submits the form
        document.getElementById('proceedBtn').addEventListener('click', function() {
            duplicateModal.hide();
            allowSubmit = true;
            form.submit();
        });
    });
</script>
@endpush

@endsection