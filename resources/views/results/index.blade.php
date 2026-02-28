@extends('layouts.app')

@section('title', 'View Results')

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">View All Student Results</h6>
        <div>
            <a href="{{ route('results.single-upload') }}" class="btn btn-primary btn-sm me-2">
                <i data-lucide="plus" style="width: 14px;" class="me-1"></i>Add Single Result
            </a>
            <a href="{{ route('results.bulk-upload') }}" class="btn btn-primary btn-sm me-2">
                <i data-lucide="upload" style="width: 14px;" class="me-1"></i>Bulk Upload
            </a>
            <a href="{{ route('results.export', request()->query()) }}" class="btn btn-success btn-sm">
                <i data-lucide="download" style="width: 14px;" class="me-1"></i>Export CSV
            </a>
        </div>
    </div>
</div>

@include('includes.message')

<div class="card mb-4">
    <div class="card-body">
        <h6 class="card-title fw-bold mb-4">Filters</h6>

        <form method="GET" action="{{ route('results.index') }}" class="filter-form">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Academic Year</label>
                    <select name="academic_year_id" class="form-select">
                        <option value="">All Years</option>
                        @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                            {{ $year->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Academic Period</label>
                    <select name="academic_period_id" class="form-select">
                        <option value="">All Periods</option>
                        @foreach($academicPeriods as $period)
                        <option value="{{ $period->id }}" {{ request('academic_period_id') == $period->id ? 'selected' : '' }}>
                            {{ $period->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Subject</label>
                    <select name="subject_id" class="form-select">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Class</label>
                    <select name="class_id" class="form-select">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Sort By</label>
                    <div class="d-flex gap-2">
                        <select name="sort_by" class="form-select">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date Added</option>
                            <option value="score" {{ request('sort_by') == 'score' ? 'selected' : '' }}>Score</option>
                        </select>
                        <select name="sort_order" class="form-select" style="max-width: 150px;">
                            <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                            <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="search" style="width: 14px;" class="me-2"></i>Filter Results
                        </button>
                        <a href="{{ route('results.index') }}" class="btn btn-light">
                            <i data-lucide="x" style="width: 14px;" class="me-2"></i>Clear Filters
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h6 class="card-title fw-bold mb-4">
            Results <span class="badge bg-primary">{{ count($results) }}</span>
        </h6>

        @if($results->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        @foreach($assessmentNames as $assessment)
                        <th>{{ $assessment }}</th>
                        @endforeach
                        <th class="fw-bold">Final Score</th>
                        <th>Period</th>
                        <th>Year</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $data)
                    <tr>
                        <td>
                            <span class="badge bg-info">{{ $data['student']->student_id }}</span>
                        </td>
                        <td>
                            <strong>{{ $data['student']->first_name }} {{ $data['student']->last_name }}</strong>
                        </td>
                        @foreach($assessmentNames as $assessment)
                        <td>
                            @if(isset($data['scores'][$assessment]))
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold">{{ number_format($data['scores'][$assessment], 2) }}</span>
                                <div class="progress" style="height: 6px; width: 50px;">
                                    <div class="progress-bar {{ $data['scores'][$assessment] >= 70 ? 'bg-success' : ($data['scores'][$assessment] >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                        style="width: {{ min($data['scores'][$assessment], 100) }}%">
                                    </div>
                                </div>

                            </div>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        @endforeach
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold badge {{ $data['finalScore'] >= 70 ? 'bg-success' : ($data['finalScore'] >= 50 ? 'bg-warning' : 'bg-danger') }}">
                                    {{ number_format($data['finalScore'], 2) }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <small>{{ $data['academicPeriod']->name }}</small>
                        </td>
                        <td>
                            <small>{{ $data['academicYear']->name }}</small>
                        </td>
                        <td>
                            <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i data-lucide="more-horizontal" style="width: 14px;"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item view-details-btn" href="#"
                                        data-student-id="{{ $data['student']->student_id }}"
                                        data-student-name="{{ $data['student']->first_name }} {{ $data['student']->last_name }}"
                                        data-scores="{{ json_encode($data['scores']) }}"
                                        data-assessments="{{ json_encode($assessmentNames) }}">
                                        <i data-lucide="eye" style="width: 14px;" class="me-2"></i>View Details
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger delete-row-btn" href="#"
                                        data-student-id="{{ $data['student']->id }}"
                                        data-student-name="{{ $data['student']->first_name }} {{ $data['student']->last_name }}"
                                        data-year-id="{{ $data['academicYear']->id }}"
                                        data-period-id="{{ $data['academicPeriod']->id }}">
                                        <i data-lucide="trash-2" style="width: 14px;" class="me-2"></i>Delete All Results
                                    </a>
                                </li>
                            </ul>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <small class="text-muted">
                Showing {{ $results->count() }} students
            </small>
            <div>
                {{ $results->appends(request()->query())->links() }}
            </div>
        </div>
        @else
        <div class="alert alert-info text-center py-5">
            <i data-lucide="inbox" style="width: 48px; height: 48px;" class="d-block mx-auto mb-3 text-muted"></i>
            <p class="mb-0">No results found. Try adjusting your filters or upload some results.</p>
        </div>
        @endif
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailsModalLabel">Student Assessment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <h6 class="fw-bold">Student Information</h6>
                    <p class="mb-1"><strong>Student ID:</strong> <span id="detailStudentId" class="badge bg-info"></span></p>
                    <p class="mb-0"><strong>Name:</strong> <span id="detailStudentName"></span></p>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold">Assessment Scores</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Assessment</th>
                                    <th class="text-end">Score</th>
                                    <th class="text-center">Progress</th>
                                </tr>
                            </thead>
                            <tbody id="detailScoresTable">
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="alert alert-primary">
                    <strong>Final Score:</strong> <span id="detailFinalScore" class="badge bg-primary fs-6"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteConfirmLabel">
                    <i data-lucide="alert-triangle" style="width: 20px;" class="me-2"></i>Delete Result
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the <strong id="deleteAssessmentName"></strong> result?</p>
                <p class="text-muted small mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Row Confirmation Modal -->
<div class="modal fade" id="deleteRowConfirmModal" tabindex="-1" aria-labelledby="deleteRowConfirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteRowConfirmLabel">
                    <i data-lucide="alert-triangle" style="width: 20px;" class="me-2"></i>Delete All Student Results
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong>all results</strong> for <strong id="deleteRowStudentName"></strong>?</p>
                <div class="alert alert-warning mb-0">
                    <i data-lucide="alert-circle" style="width: 16px;" class="me-2"></i>
                    <strong>Warning:</strong> This will delete all assessment scores for this student in the selected period. This action cannot be undone.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteRowBtn">Delete All</button>
            </div>
        </div>
    </div>
</div>

<script>
    let detailsModal = null;
    let deleteConfirmModal = null;
    let deleteRowConfirmModal = null;
    let deleteResultId = null;
    let deleteRowData = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize modal instances once
        detailsModal = new bootstrap.Modal(document.getElementById('viewDetailsModal'));
        deleteConfirmModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
        deleteRowConfirmModal = new bootstrap.Modal(document.getElementById('deleteRowConfirmModal'));

        // View details button click handler
        document.querySelectorAll('.view-details-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const studentId = this.getAttribute('data-student-id');
                const studentName = this.getAttribute('data-student-name');
                const scores = JSON.parse(this.getAttribute('data-scores'));
                const assessments = JSON.parse(this.getAttribute('data-assessments'));
                showStudentDetails(studentId, studentName, scores, assessments);
                detailsModal.show();
            });
        });

        // Delete row button click handler
        document.querySelectorAll('.delete-row-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const studentId = this.getAttribute('data-student-id');
                const studentName = this.getAttribute('data-student-name');
                const yearId = this.getAttribute('data-year-id');
                const periodId = this.getAttribute('data-period-id');
                confirmDeleteRow(studentId, studentName, yearId, periodId);
            });
        });

        // Delete assessment button click handler
        document.querySelectorAll('.delete-assessment-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const resultId = this.getAttribute('data-result-id');
                const assessmentName = this.getAttribute('data-assessment-name');
                confirmDelete(resultId, assessmentName);
            });
        });

        // Delete single result confirmation button click
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (deleteResultId) {
                deleteResult(deleteResultId);
            }
        });

        // Delete entire row confirmation button click
        document.getElementById('confirmDeleteRowBtn').addEventListener('click', function() {
            if (deleteRowData) {
                deleteStudentResults(deleteRowData);
            }
        });
    });

    function showStudentDetails(studentId, studentName, scores, assessmentNames) {
        // Set student information
        document.getElementById('detailStudentId').textContent = studentId;
        document.getElementById('detailStudentName').textContent = studentName;

        // Parse JSON strings if needed
        scores = typeof scores === 'string' ? JSON.parse(scores) : scores;
        assessmentNames = typeof assessmentNames === 'string' ? JSON.parse(assessmentNames) : assessmentNames;

        // Build scores table
        const scoresTable = document.getElementById('detailScoresTable');
        scoresTable.innerHTML = '';

        let totalScore = 0;
        assessmentNames.forEach(assessment => {
            const score = scores[assessment] || 0;
            totalScore += parseFloat(score);

            const row = document.createElement('tr');
            const scoreNum = parseFloat(score);
            const badgeClass = scoreNum >= 70 ? 'bg-success' : (scoreNum >= 50 ? 'bg-warning' : 'bg-danger');

            row.innerHTML = `
            <td>${assessment}</td>
            <td class="text-end fw-bold">${scoreNum.toFixed(2)}</td>
            <td class="text-center">
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar ${badgeClass}" style="width: ${Math.min(scoreNum, 100)}%">
                        ${scoreNum.toFixed(1)}%
                    </div>
                </div>
            </td>
        `;
            scoresTable.appendChild(row);
        });

        // Set final score
        const finalScoreBadge = document.getElementById('detailFinalScore');
        const finalScore = totalScore.toFixed(2);
        finalScoreBadge.textContent = finalScore;
        finalScoreBadge.className = 'badge fs-6 ' +
            (totalScore >= 70 ? 'bg-success' : (totalScore >= 50 ? 'bg-warning' : 'bg-danger'));

        // Show modal (reuse instance)
        detailsModal.show();
    }

    function confirmDelete(resultId, assessmentName) {
        deleteResultId = resultId;
        document.getElementById('deleteAssessmentName').textContent = assessmentName;
        deleteConfirmModal.show();
    }

    function confirmDeleteRow(studentId, studentName, academicYearId, academicPeriodId) {
        deleteRowData = {
            student_id: studentId,
            academic_year_id: academicYearId,
            academic_period_id: academicPeriodId
        };
        document.getElementById('deleteRowStudentName').textContent = studentName;
        deleteRowConfirmModal.show();
    }

    function deleteResult(resultId) {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
            document.querySelector('input[name="_token"]')?.value;

        fetch(`/results/${resultId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-Token': token,
                    'Content-Type': 'application/json',
                }
            })
            .then(response => {
                if (response.ok) {
                    deleteConfirmModal.hide();
                    // Reload the page to reflect changes
                    window.location.reload();
                } else {
                    alert('Error deleting result. Please try again.');
                    deleteConfirmModal.hide();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting result. Please try again.');
                deleteConfirmModal.hide();
            });
    }

    function deleteStudentResults(data) {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
            document.querySelector('input[name="_token"]')?.value;

        fetch('/results/delete-student-results', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-Token': token,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                console.log('Response status:', response.status);
                const isJson = response.headers.get('content-type')?.includes('application/json');
                return isJson ? response.json() : Promise.reject(new Error('Invalid response format'));
            })
            .then(body => {
                console.log('Success response:', body);
                // Show success message BEFORE hiding modal
                if (body.message) {
                    alert(body.message);
                } else {
                    alert('Results deleted successfully!');
                }
                // Hide modal after alert is shown
                deleteRowConfirmModal.hide();
                // Reload the page after alert is dismissed
                setTimeout(() => window.location.reload(), 500);
            })
            .catch(error => {
                console.error('Delete error:', error);
                deleteRowConfirmModal.hide();
                alert('Error deleting results: ' + (error.message || 'Please try again.'));
            });
    }
</script>

@endsection