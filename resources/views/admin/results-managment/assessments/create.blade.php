@extends('layouts.app')

@section('title', 'Configure Assessment')

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center">
        <a href="{{ route('admin.results-management.assessments.index') }}" class="btn btn-outline-secondary btn-sm me-3">
            <i data-lucide="arrow-left" style="width: 14px;"></i>
        </a>
        <h6 class="mb-0 text-uppercase fw-bold">Configure New Subject Assessment</h6>
    </div>
</div>

@include('includes.message')

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.results-management.assessments.store') }}" method="POST" id="assessmentForm">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Select Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                                <option value="">Choose a subject...</option>
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->type }})</option>
                                @endforeach
                            </select>
                            @error('subject_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Target Class Levels <span class="text-danger">*</span></label>
                            <div class="p-3 border rounded @error('class_level_ids') border-danger @enderror" style="max-height: 150px; overflow-y: auto;">
                                @foreach($classes as $class)
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox" name="class_level_ids[]" value="{{ $class->id }}" id="class_{{ $class->id }}"
                                        {{ (is_array(old('class_level_ids')) && in_array($class->id, old('class_level_ids'))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="class_{{ $class->id }}">
                                        {{ $class->name }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            @error('class_level_ids')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="border-top pt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-uppercase">Assessment Components</h6>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="addComponent">
                                <i data-lucide="plus" class="me-1" style="width: 14px;"></i> Add Row
                            </button>
                        </div>

                        <div id="componentContainer">
                            <!-- Initial Component Row -->
                            <div class="row g-3 mb-3 component-row align-items-end">
                                <div class="col-md-7">
                                    <label class="form-label small text-muted">Component Name (e.g. Class Test 1)</label>
                                    <input type="text" name="components[0][name]" class="form-control" placeholder="Enter name" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small text-muted">Weight (%)</label>
                                    <div class="input-group">
                                        <input type="number" name="components[0][percentage]" class="form-control weight-input" placeholder="0" min="0.01" max="100" step="0.01" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-danger w-100 remove-row" disabled>
                                        <i data-lucide="x" style="width: 14px;"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light border-0">
                                    <div class="card-body d-flex justify-content-between align-items-center py-2">
                                        <span class="fw-bold">Total Weighting:</span>
                                        <div class="d-flex align-items-center">
                                            <h4 class="mb-0 me-2" id="totalWeightDisplay">0%</h4>
                                            <span id="weightStatus" class="badge bg-secondary">Incomplete</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="small text-muted mt-2">
                                    <i data-lucide="info" class="me-1" style="width: 12px;"></i> Note: The sum of all component weights must equal exactly 100%.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="reset" class="btn btn-light me-2">Clear Form</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>Save Configuration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let rowCount = 1;
        const container = document.getElementById('componentContainer');
        const addBtn = document.getElementById('addComponent');
        const totalDisplay = document.getElementById('totalWeightDisplay');
        const weightStatus = document.getElementById('weightStatus');
        const submitBtn = document.getElementById('submitBtn');
        const weightInputs = document.getElementsByClassName('weight-input');

        // Initialize total calculation
        updateTotal();

        // Event listener for all existing and future weight inputs
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('weight-input')) {
                updateTotal();
            }
        });

        function updateTotal() {
            let total = 0;
            const inputs = document.querySelectorAll('.weight-input');

            inputs.forEach(input => {
                const value = parseFloat(input.value) || 0;
                total += value;
            });

            // Round to 2 decimal places for display
            const roundedTotal = Math.round(total * 100) / 100;
            totalDisplay.innerText = roundedTotal + '%';

            if (Math.abs(roundedTotal - 100) < 0.01) { // Allow small floating point differences
                weightStatus.innerText = 'Perfect';
                weightStatus.className = 'badge bg-success';
                submitBtn.disabled = false;
            } else if (roundedTotal > 100) {
                weightStatus.innerText = 'Exceeded 100%';
                weightStatus.className = 'badge bg-danger';
                submitBtn.disabled = true;
            } else {
                weightStatus.innerText = 'Incomplete';
                weightStatus.className = 'badge bg-warning text-dark';
                submitBtn.disabled = true;
            }
        }

        addBtn.addEventListener('click', function() {
            const row = document.createElement('div');
            row.className = 'row g-3 mb-3 component-row align-items-end';
            row.innerHTML = `
            <div class="col-md-7">
                <input type="text" name="components[${rowCount}][name]" class="form-control" placeholder="Enter name" required>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <input type="number" name="components[${rowCount}][percentage]" class="form-control weight-input" placeholder="0" min="0.01" max="100" step="0.01" required>
                    <span class="input-group-text">%</span>
                </div>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger w-100 remove-row">
                    <i data-lucide="x" style="width: 14px;"></i>
                </button>
            </div>
        `;
            container.appendChild(row);

            // Enable remove button on the first row if there are multiple rows
            if (rowCount === 1) {
                document.querySelector('.remove-row').disabled = false;
            }

            // Re-initialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Add event listener to the new remove button
            row.querySelector('.remove-row').addEventListener('click', function() {
                row.remove();
                updateTotal();

                // Disable remove button on first row if only one row remains
                const rows = document.querySelectorAll('.component-row');
                if (rows.length === 1) {
                    rows[0].querySelector('.remove-row').disabled = true;
                }
            });

            rowCount++;
            updateTotal(); // Update total after adding new row
        });

        // Add event listener to initial remove button
        document.querySelector('.remove-row').addEventListener('click', function(e) {
            if (!e.target.disabled) {
                e.target.closest('.component-row').remove();
                updateTotal();
            }
        });

        // Reset form functionality
        document.querySelector('button[type="reset"]').addEventListener('click', function() {
            setTimeout(function() {
                // Reset row count and update total
                const rows = document.querySelectorAll('.component-row');
                rowCount = rows.length;
                updateTotal();

                // Disable remove button on first row if only one row exists
                if (rows.length === 1) {
                    rows[0].querySelector('.remove-row').disabled = true;
                }
            }, 100);
        });
    });
</script>
@endpush
@endsection