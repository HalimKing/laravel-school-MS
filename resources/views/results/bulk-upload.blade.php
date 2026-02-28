@extends('layouts.app')

@section('title', 'Bulk Results Upload')

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center">
        <h6 class="mb-0 text-uppercase fw-bold">Bulk Upload Student Results</h6>
    </div>
</div>

@include('includes.message')

@if(session('partial_success'))
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <h5 class="alert-heading fw-bold mb-3">
        <i data-lucide="alert-triangle" style="width: 18px;" class="me-2"></i>{{ session('partial_success') }}
    </h5>
    @if(session('errors'))
    <h6 class="fw-bold mt-3 mb-2">Errors:</h6>
    <ul class="mb-0" style="max-height: 300px; overflow-y: auto;">
        @foreach(session('errors') as $error)
        <li class="text-dark">{{ $error }}</li>
        @endforeach
    </ul>
    @endif
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

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
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title fw-bold mb-4">Upload Results File</h6>

                <form action="{{ route('results.bulk-upload.store') }}" method="POST" enctype="multipart/form-data" id="bulkUploadForm">
                    @csrf

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Academic Year <span class="text-danger">*</span></label>
                            <select name="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
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
                            <select name="academic_period_id" class="form-select @error('academic_period_id') is-invalid @enderror" required>
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
                            <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
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
                            <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
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

                    <div class="mb-4">
                        <label class="form-label fw-bold">Upload File (CSV or Excel) <span class="text-danger">*</span></label>
                        <div class="card border-2 border-dashed p-4" id="dropZone">
                            <div class="text-center">
                                <i data-lucide="upload-cloud" style="width: 48px; height: 48px;" class="text-muted mb-3"></i>
                                <h6 class="fw-bold">Drag and drop your file here</h6>
                                <p class="text-muted small">or click to browse</p>
                                <input type="file" name="file" id="fileInput" class="d-none @error('file') is-invalid @enderror"
                                    accept=".csv,.xlsx,.xls,.txt" required>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="browseBtn">
                                    <i data-lucide="folder-open" class="me-2" style="width: 14px;"></i>Browse Files
                                </button>
                                <div id="fileName" class="mt-2 text-success fw-bold d-none"></div>
                            </div>
                        </div>
                        @error('file')
                        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mt-4 text-end">
                        <button type="reset" class="btn btn-light me-2">Clear</button>
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="upload" class="me-2" style="width: 16px;"></i>Upload Results
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Instructions Card -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0 fw-bold">
                    <i data-lucide="info" style="width: 18px;" class="me-2"></i>File Format Guide
                </h6>
            </div>
            <div class="card-body">
                <h6 class="fw-bold text-danger mb-3">Format: Each Assessment is a Column</h6>
                <div class="alert alert-info small mb-3">
                    <strong>Note:</strong> First column must be Registration Number, followed by assessment names as column headers.
                </div>

                <h6 class="fw-bold mt-2 mb-2">Example:</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>Registration Number</th>
                                <th>Quiz</th>
                                <th>Mid-Term</th>
                                <th>Exams</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <tr>
                                <td>S0020</td>
                                <td>10</td>
                                <td>20</td>
                                <td>70</td>
                            </tr>
                            <tr>
                                <td>S0021</td>
                                <td>15</td>
                                <td>25</td>
                                <td>75</td>
                            </tr>
                            <tr>
                                <td>S0022</td>
                                <td>12</td>
                                <td>18</td>
                                <td>65</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info mt-3 mb-0">
                    <small>
                        <strong>Tip:</strong> Make sure the Assessment Name matches exactly with your configured assessments for the selected subject and class.
                    </small>
                </div>
            </div>
        </div>

        <!-- Download Template -->
        <div class="card">
            <div class="card-body text-center">
                <i data-lucide="file-text" style="width: 32px; height: 32px;" class="text-primary mb-2"></i>
                <h6 class="fw-bold">Download Template</h6>
                <p class="small text-muted mb-3">Select subject and class to download a template with actual assessments</p>
                <button type="button" class="btn btn-outline-primary btn-sm w-100" id="downloadTemplate" disabled>
                    <i data-lucide="download" class="me-2" style="width: 14px;"></i>Download CSV Template
                </button>
                <small class="text-muted d-block mt-2" id="templateHelp">Select both subject and class first</small>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('fileInput');
        const browseBtn = document.getElementById('browseBtn');
        const dropZone = document.getElementById('dropZone');
        const fileName = document.getElementById('fileName');
        const downloadBtn = document.getElementById('downloadTemplate');
        const subjectSelect = document.querySelector('select[name="subject_id"]');
        const classSelect = document.querySelector('select[name="class_id"]');
        const templateHelp = document.getElementById('templateHelp');

        // Browse button
        browseBtn.addEventListener('click', () => fileInput.click());

        // File input change
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                fileName.textContent = `✓ ${this.files[0].name}`;
                fileName.classList.remove('d-none');
                dropZone.classList.add('bg-light');
            } else {
                fileName.classList.add('d-none');
                dropZone.classList.remove('bg-light');
            }
        });

        // Drag and drop
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('bg-light');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('bg-light');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('bg-light');

            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                fileName.textContent = `✓ ${e.dataTransfer.files[0].name}`;
                fileName.classList.remove('d-none');
                dropZone.classList.add('bg-light');
            }
        });

        // Check if subject and class are selected, enable/disable download button
        function updateTemplateButton() {
            const subjectId = subjectSelect.value;
            const classId = classSelect.value;

            if (subjectId && classId) {
                downloadBtn.disabled = false;
                templateHelp.textContent = 'Ready to download template with selected assessments';
                templateHelp.classList.remove('text-muted');
                templateHelp.classList.add('text-success');
            } else {
                downloadBtn.disabled = true;
                templateHelp.textContent = 'Select both subject and class first';
                templateHelp.classList.remove('text-success');
                templateHelp.classList.add('text-muted');
            }
        }

        // Update button on subject/class change
        subjectSelect.addEventListener('change', updateTemplateButton);
        classSelect.addEventListener('change', updateTemplateButton);

        // Download template
        downloadBtn.addEventListener('click', function() {
            const subjectId = subjectSelect.value;
            const classId = classSelect.value;

            if (subjectId && classId) {
                window.location.href = `{{ route('results.download-template', ['subjectId' => ':subjectId', 'classId' => ':classId']) }}`
                    .replace(':subjectId', subjectId)
                    .replace(':classId', classId);
            }
        });

        // Initialize button state
        updateTemplateButton();
    });
</script>
@endpush

@endsection