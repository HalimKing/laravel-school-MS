@extends('layouts.app')

@section('title', 'Enroll Students')

@section('content')

<div class="card mb-4 p-4">
    <h6 class="d-flex align-items-center mb-0 text-uppercase fw-bold">
        <span class="text-muted">Students</span>
        <i data-lucide="chevron-right" class="mx-2" style="width: 16px;"></i>
        <span>Enroll Students</span>
    </h6>
</div>

@include('includes.message')

<!-- Filter Section (Database Fetch) -->
<div class="card mb-4">
    <div class="card-body">
        <h6 class="card-title mb-3">Filter Students</h6>
        <form action="{{ route('admin.enrollments.enroll-students.filter') }}" method="GET" class="row align-items-end">
            <div class="col-md-4 mb-3 mb-md-0">
                <label class="form-label small fw-bold">Academic Year</label>
                <select name="filter_year" class="form-select" required>
                    <option value="">Select Year</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ request('filter_year') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <label class="form-label small fw-bold">Current Class</label>
                <select name="filter_class" class="form-select" required>
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('filter_class') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i data-lucide="filter" class="me-1" style="width: 16px;"></i> Filter Data
                </button>
            </div>
        </form>
    </div>
</div>

@if(isset($students))
<div class="card">
    <div class="card-body">
        <div class="row mb-4 align-items-center">
            <!-- Counter Display -->
            <div class="col-md-4">
                <h6 class="card-title mb-0">
                    Showing <span id="visibleCount" class="badge bg-primary">{{ $students->count() }}</span> 
                    <span class="text-muted fw-normal">of {{ $students->count() }} students</span>
                </h6>
            </div>

            <!-- Instant Search Input -->
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i data-lucide="search" style="width: 16px;"></i>
                    </span>
                    <input type="text" id="tableSearch" class="form-control border-start-0" placeholder="Search by name or ID...">
                </div>
            </div>

            <!-- Action Button -->
            <div class="col-md-4 text-end">
                <button id="proceedButton" class="btn btn-success d-none" data-bs-toggle="modal" data-bs-target="#enrollmentModal">
                    <i data-lucide="external-link" class="me-1" style="width: 18px;"></i> 
                    Proceed (<span id="btnSelectedCount">0</span>)
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle" id="studentTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </div>
                        </th>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Current Academic Year</th>
                        <th>Current Class</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr class="student-row">
                            <td>
                                <div class="form-check">
                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="form-check-input student-checkbox">
                                </div>
                            </td>
                            <td class="searchable-id"><span class="fw-bold">{{ $student->student->student_id }}</span></td>
                            <td class="searchable-name">{{ $student->student->first_name }} {{ $student->student->last_name }}</td>
                            <td>{{ $student->academicYear->name ?? 'N/A' }}</td>
                            <td>{{ $student->class->name ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                No students found for the selected criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Enrollment Modal -->
<div class="modal fade" id="enrollmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.enrollments.enroll-students.process') }}" method="POST" id="enrollmentForm">
                @csrf
                <div id="hiddenInputsContainer"></div>

                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Confirm Enrollment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2">
                        <small>
                            <i data-lucide="info" class="me-1" style="width: 14px;"></i> 
                            You are about to enroll <span id="selectedCount" class="fw-bold text-decoration-underline">0</span> students.
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Target Academic Year <span class="text-danger">*</span></label>
                        <select name="target_year" class="form-select" required>
                            <option value="">Select Destination Year</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}">{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Target Class <span class="text-danger">*</span></label>
                        <select name="target_class" class="form-select" required>
                            <option value="">Select Destination Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Complete Enrollment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.student-checkbox');
        const proceedButton = document.getElementById('proceedButton');
        const btnSelectedCount = document.getElementById('btnSelectedCount');
        const selectedCountModal = document.getElementById('selectedCount');
        const hiddenInputsContainer = document.getElementById('hiddenInputsContainer');
        
        // Search Variables
        const searchInput = document.getElementById('tableSearch');
        const visibleCountSpan = document.getElementById('visibleCount');
        const tableRows = document.querySelectorAll('.student-row');

        /**
         * 1. Dynamic UI Refresh
         * Updates counts on the button and inside the modal
         */
        function updateSelectionUI() {
            const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;
            
            if (checkedCount > 0) {
                proceedButton.classList.remove('d-none');
                btnSelectedCount.textContent = checkedCount;
                if(selectedCountModal) selectedCountModal.textContent = checkedCount;
            } else {
                proceedButton.classList.add('d-none');
            }
        }

        /**
         * 2. Real-time Table Filtering
         */
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                let visibleCount = 0;

                tableRows.forEach(row => {
                    const studentId = row.querySelector('.searchable-id').textContent.toLowerCase();
                    const studentName = row.querySelector('.searchable-name').textContent.toLowerCase();
                    
                    if (studentId.includes(query) || studentName.includes(query)) {
                        row.style.display = "";
                        visibleCount++;
                    } else {
                        row.style.display = "none";
                        // Optional: Uncheck row if it's hidden by search
                        // row.querySelector('.student-checkbox').checked = false;
                    }
                });
                
                visibleCountSpan.textContent = visibleCount;
                
                // Visual feedback if no results
                if (visibleCount === 0) {
                    visibleCountSpan.classList.replace('bg-primary', 'bg-danger');
                } else {
                    visibleCountSpan.classList.replace('bg-danger', 'bg-primary');
                }

                updateSelectionUI();
            });
        }

        /**
         * 3. Select All Logic (Filtered-aware)
         */
        if(selectAll) {
            selectAll.addEventListener('change', function() {
                tableRows.forEach(row => {
                    // Only select rows that are currently visible
                    if (row.style.display !== 'none') {
                        const cb = row.querySelector('.student-checkbox');
                        cb.checked = this.checked;
                    }
                });
                updateSelectionUI();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateSelectionUI);
        });

        /**
         * 4. Prepare Modal Data
         * Transfers checkbox IDs to hidden inputs in the form
         */
        const enrollmentModal = document.getElementById('enrollmentModal');
        if(enrollmentModal) {
            enrollmentModal.addEventListener('show.bs.modal', function () {
                hiddenInputsContainer.innerHTML = '';
                document.querySelectorAll('.student-checkbox:checked').forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'student_ids[]';
                    input.value = cb.value;
                    hiddenInputsContainer.appendChild(input);
                });
            });
        }
    });
</script>
@endpush