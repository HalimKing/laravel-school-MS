@extends('layouts.app')

@section('title', 'Subjects Management')

@section('content')

<div class="card mb-4 p-3 shadow-sm border-0">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="d-flex align-items-center mb-0 text-uppercase fw-bold">
            <span class="text-muted">Academics</span>
            <i data-lucide="chevron-right" class="mx-2" style="width: 16px;"></i>
            <span>Subjects</span>
        </h6>
        <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
            <i data-lucide="plus-circle" class="me-2" style="width: 18px;"></i>
            Add New Subject
        </button>
    </div>
</div>

@include('includes.message')

<!-- Statistics Overview -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="bg-soft-primary p-3 rounded-circle me-3">
                    <i data-lucide="book-open" class="text-primary"></i>
                </div>
                <div>
                    <p class="text-muted mb-0 small">Total Subjects</p>
                    <h4 class="mb-0 fw-bold">{{ $subjects->count() }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="bg-soft-info p-3 rounded-circle me-3">
                    <i data-lucide="file-text" class="text-info"></i>
                </div>
                <div>
                    <p class="text-muted mb-0 small">Core</p>
                    <h4 class="mb-0 fw-bold">{{ $subjects->where('type', 'core')->count() }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="bg-soft-success p-3 rounded-circle me-3">
                    <i data-lucide="flask-conical" class="text-success"></i>
                </div>
                <div>
                    <p class="text-muted mb-0 small">Elective</p>
                    <h4 class="mb-0 fw-bold">{{ $subjects->where('type', 'elective')->count() }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Subjects Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-muted small fw-bold text-uppercase">Code</th>
                        <th class="py-3 text-muted small fw-bold text-uppercase">Subject Name</th>
                        <th class="py-3 text-muted small fw-bold text-uppercase">Type</th>
                        <th class="py-3 text-muted small fw-bold text-uppercase text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $subject)
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-light text-dark border fw-bold">{{ $subject->code }}</span>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $subject->name }}</div>
                        </td>
                        <td>
                            @if($subject->type == 'core')
                                <span class="badge bg-soft-info text-info rounded-pill px-3">
                                    <i data-lucide="file-text" class="me-1" style="width: 12px;"></i> core
                                </span>
                            @else
                                <span class="badge bg-soft-success text-success rounded-pill px-3">
                                    <i data-lucide="flask-conical" class="me-1" style="width: 12px;"></i> Elective
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('admin.academics.subjects.edit', $subject->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i data-lucide="edit-3" style="width: 14px;"></i>
                                </a>
                                <form action="{{ route('admin.academics.subjects.destroy', $subject->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this subject?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger ms-1" title="Delete">
                                        <i data-lucide="trash-2" style="width: 14px;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <div class="py-4">
                                <i data-lucide="inbox" class="text-muted mb-2" style="width: 48px; height: 48px; opacity: 0.3;"></i>
                                <p class="text-muted">No subjects found in the database.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1" aria-labelledby="addSubjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="addSubjectModalLabel">New Subject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.academics.subjects.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Subject Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Mathematics" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Subject Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" placeholder="e.g. MATH101" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Subject Type <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3 mt-1">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="typeCore" value="core" checked>
                                <label class="form-check-label" for="typeCore">Core</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="typeElective" value="elective">
                                <label class="form-check-label" for="typeElective">Elective</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary { background-color: rgba(13, 110, 253, 0.1); }
    .bg-soft-info { background-color: rgba(13, 202, 240, 0.1); }
    .bg-soft-success { background-color: rgba(25, 135, 84, 0.1); }
    .bg-soft-danger { background-color: rgba(220, 53, 69, 0.1); }
</style>

@endsection