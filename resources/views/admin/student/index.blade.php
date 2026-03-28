@extends('layouts.app')

@section('title', 'All Students')

@section('content')

<div class="card mb-4 p-4">
    <h6 class="mb-0 text-uppercase fw-bold">Student Management</h6>
</div>

@include('includes.message')

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
                    <h6 class="card-title mb-3 mb-md-0">Student List</h6>

                    <div class="d-flex gap-2">
                        <!-- Server-side Search Form -->
                        <form action="{{ route('admin.students.index') }}" method="GET" class="d-flex">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search students..." value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i data-lucide="search" style="width: 16px;"></i>
                                </button>
                                @if(request('search'))
                                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-danger">Clear</a>
                                @endif
                            </div>
                        </form>

                        <a class="btn btn-primary" href="{{ route('admin.students.create') }}">
                            <i data-lucide="user-plus" class="me-1" style="width: 18px;"></i> Add Student
                        </a>

                        <button class="btn btn-outline-success" type="button" data-bs-toggle="modal" data-bs-target="#studentImportModal">
                            <i data-lucide="upload" class="me-1" style="width: 18px;"></i> Import Students
                        </button>

                        <a class="btn btn-outline-secondary" href="{{ route('admin.students.import.template') }}">
                            <i data-lucide="download" class="me-1" style="width: 18px;"></i> Sample Template
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Student Details</th>
                                <th>Date of Birth</th>
                                <th>Class</th>
                                <th>Gender</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                            <tr>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                            <i data-lucide="graduation-cap" class="text-secondary" style="width: 20px;"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold d-block">{{ $student->first_name }} {{ $student->last_name }}</span>
                                            <small class="text-muted">{{ $student->student_id ?? 'No ID' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <span class="badge text-primary border">{{ $student->date_of_birth }}</span>
                                </td>
                                <td class="align-middle">
                                    {{ $student->latestLevel->class->name ?? 'Not Assigned' }}
                                </td>
                                <td class="align-middle text-capitalize">
                                    {{ $student->gender }}
                                </td>
                                <td class="align-middle">
                                    @if($student->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                    @else
                                    <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end align-middle">
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.students.show', $student->id) }}">
                                                    <i data-lucide="eye" class="me-2 text-muted" style="width: 14px;"></i> View Profile
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.students.edit', $student->id) }}">
                                                    <i data-lucide="pencil" class="me-2 text-muted" style="width: 14px;"></i> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <form method="POST" action="{{ route('admin.students.destroy', $student->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger d-flex align-items-center" onclick="return confirm('Are you sure?')">
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
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <p class="mb-0">No student records found.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links with Bootstrap Styling -->
                <div class="mt-4 d-flex justify-content-center float-end">
                    {{ $students->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>


</div>

<!-- Student Import Modal -->
<div class="modal fade" id="studentImportModal" tabindex="-1" aria-labelledby="studentImportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="studentImportModalLabel">
                    <i data-lucide="file-up" class="me-2" style="width: 18px;"></i>
                    Import Students
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="studentImportForm" action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info mb-3" role="alert">
                        Upload a CSV or Excel file to import multiple students at once.
                        <a href="{{ route('admin.students.import.template') }}" class="alert-link">Download sample template</a>.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="importFile" class="form-label fw-semibold">Import File</label>
                            <input type="file" class="form-control" id="importFile" name="file" accept=".csv,.txt,.xlsx,.xls" required>
                            <small class="text-muted">Accepted formats: CSV, XLSX, XLS. Max size: 10MB.</small>
                        </div>

                        <div class="col-md-4">
                            <label for="duplicateStrategy" class="form-label fw-semibold">If Student ID Exists</label>
                            <select class="form-select" id="duplicateStrategy" name="duplicate_strategy" required>
                                <option value="skip" selected>Skip Existing</option>
                                <option value="update">Update Existing</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Expected Columns</label>
                            <div class="border rounded p-3 bg-light-subtle">
                                <div class="row g-2 small">
                                    <div class="col-md-4"><span class="fw-semibold">Required:</span> student_name, student_id, class, gender, date_of_birth</div>
                                    <div class="col-md-4"><span class="fw-semibold">Optional:</span> email, status, parent_name, parent_phone</div>
                                    <div class="col-md-4"><span class="fw-semibold">Optional:</span> address, academic_year, first_name, last_name</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="rowsJsonPayload" name="rows_json" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i data-lucide="check-circle" class="me-1" style="width: 16px;"></i>
                        Start Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const importForm = document.getElementById('studentImportForm');
        const importFile = document.getElementById('importFile');
        const rowsJsonPayload = document.getElementById('rowsJsonPayload');

        if (!importForm || !importFile || !rowsJsonPayload) {
            return;
        }

        importForm.addEventListener('submit', function(e) {
            const file = importFile.files && importFile.files.length ? importFile.files[0] : null;
            if (!file) {
                return;
            }

            const extension = (file.name.split('.').pop() || '').toLowerCase();

            // CSV can be handled directly by the backend parser.
            if (extension === 'csv' || extension === 'txt') {
                rowsJsonPayload.value = '';
                return;
            }

            // For Excel, parse on the client then post JSON payload for reliable import.
            if ((extension === 'xlsx' || extension === 'xls') && !importForm.dataset.parsed) {
                e.preventDefault();

                if (typeof XLSX === 'undefined') {
                    alert('Excel parser failed to load. Please use CSV or refresh and try again.');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(event) {
                    try {
                        const workbook = XLSX.read(event.target.result, {
                            type: 'array'
                        });
                        const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                        const rows = XLSX.utils.sheet_to_json(firstSheet, {
                            defval: ''
                        });

                        if (!rows.length) {
                            alert('The selected Excel file has no data rows.');
                            return;
                        }

                        rowsJsonPayload.value = JSON.stringify(rows);
                        importForm.dataset.parsed = '1';
                        importForm.submit();
                    } catch (err) {
                        alert('Could not parse Excel file. Please verify the format or use CSV.');
                    }
                };

                reader.readAsArrayBuffer(file);
            }
        });

        const modal = document.getElementById('studentImportModal');
        if (modal) {
            modal.addEventListener('hidden.bs.modal', function() {
                importForm.dataset.parsed = '';
                rowsJsonPayload.value = '';
                importForm.reset();
            });
        }
    });
</script>
@endpush