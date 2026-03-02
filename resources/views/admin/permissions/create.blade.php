@extends('layouts.app')

@section('title', 'Create Permission')

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">Create New Permission</h6>
        <div>
            <a href="{{ route('admin.access-control.permissions.index') }}" class="btn btn-outline-secondary btn-sm">
                <i data-lucide="arrow-left" style="width: 14px;" class="me-1"></i>Back
            </a>
        </div>
    </div>
</div>

@include('includes.message')

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs mb-4" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="single-tab" data-bs-toggle="tab" data-bs-target="#single" type="button" role="tab">
                            <i data-lucide="plus" style="width: 14px;" class="me-1"></i>Single Permission
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="bulk-tab" data-bs-toggle="tab" data-bs-target="#bulk" type="button" role="tab">
                            <i data-lucide="package" style="width: 14px;" class="me-1"></i>Bulk Create
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Single Permission -->
                    <div class="tab-pane fade show active" id="single" role="tabpanel">
                        <form action="{{ route('admin.access-control.permissions.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">Permission Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" placeholder="e.g., user.create, user.edit"
                                    value="{{ old('name') }}" required>
                                <small class="text-muted d-block mt-1">Use dot notation: module.action (e.g., user.create)</small>
                                @error('name')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="category" class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                <select class="form-control @error('category') is-invalid @enderror"
                                    id="category" name="category" required>
                                    <option value="">-- Select Category --</option>
                                    <option value="user" {{ old('category') === 'user' ? 'selected' : '' }}>User Management</option>
                                    <option value="role" {{ old('category') === 'role' ? 'selected' : '' }}>Role & Permission</option>
                                    <option value="attendance" {{ old('category') === 'attendance' ? 'selected' : '' }}>Attendance</option>
                                    <option value="academic" {{ old('category') === 'academic' ? 'selected' : '' }}>Academic</option>
                                    <option value="fee" {{ old('category') === 'fee' ? 'selected' : '' }}>Fee Management</option>
                                    <option value="report" {{ old('category') === 'report' ? 'selected' : '' }}>Reports</option>
                                    <option value="setting" {{ old('category') === 'setting' ? 'selected' : '' }}>Settings</option>
                                </select>
                                @error('category')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label fw-bold">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                    id="description" name="description" rows="3"
                                    placeholder="Brief description of this permission">{{ old('description') }}</textarea>
                                @error('description')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i data-lucide="save" style="width: 14px;" class="me-1"></i>Create Permission
                                </button>
                                <a href="{{ route('admin.access-control.permissions.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>

                    <!-- Bulk Create -->
                    <div class="tab-pane fade" id="bulk" role="tabpanel">
                        <form action="{{ route('admin.access-control.permissions.bulk-create') }}" method="POST">
                            @csrf

                            <div class="alert alert-info mb-3">
                                <strong>Create multiple permissions at once.</strong> Enter category and comma-separated actions.
                            </div>

                            <div class="mb-3">
                                <label for="bulk-category" class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                <select class="form-control" id="bulk-category" name="category" required>
                                    <option value="">-- Select Category --</option>
                                    <option value="user">User Management</option>
                                    <option value="role">Role & Permission</option>
                                    <option value="attendance">Attendance</option>
                                    <option value="academic">Academic</option>
                                    <option value="fee">Fee Management</option>
                                    <option value="report">Reports</option>
                                    <option value="setting">Settings</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="actions" class="form-label fw-bold">Actions <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="actions" name="actions" rows="4"
                                    placeholder="Enter actions separated by commas&#10;Example: create, read, update, delete"
                                    required></textarea>
                                <small class="text-muted d-block mt-1">
                                    Will create permissions like: category.action
                                </small>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i data-lucide="save" style="width: 14px;" class="me-1"></i>Create Permissions
                                </button>
                                <a href="{{ route('admin.access-control.permissions.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="card-title fw-bold mb-0">Information</h6>
            </div>
            <div class="card-body">
                <p class="text-sm text-muted">
                    <strong>Permission</strong> defines a specific action users can perform in the system.
                </p>
                <hr>
                <p class="text-sm"><strong>Naming Convention:</strong></p>
                <ul class="text-sm text-muted">
                    <li><code>user.create</code> - Create user</li>
                    <li><code>user.edit</code> - Edit user</li>
                    <li><code>user.delete</code> - Delete user</li>
                    <li><code>user.view</code> - View user</li>
                </ul>
                <hr>
                <p class="text-sm text-muted">
                    Use consistent naming across all categories for better organization.
                </p>
            </div>
        </div>
    </div>
</div>

@endsection