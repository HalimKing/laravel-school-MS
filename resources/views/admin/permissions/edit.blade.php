@extends('layouts.app')

@section('title', 'Edit Permission: ' . $permission->name)

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">Edit Permission: <code>{{ $permission->name }}</code></h6>
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
                <form action="{{ route('admin.access-control.permissions.update', $permission->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Permission Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                            id="name" name="name" placeholder="e.g., user.create"
                            value="{{ old('name', $permission->name) }}" required>
                        <small class="text-muted d-block mt-1">Use dot notation: module.action</small>
                        @error('name')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                        <select class="form-control @error('category') is-invalid @enderror"
                            id="category" name="category" required>
                            <option value="">-- Select Category --</option>
                            <option value="user" {{ old('category', $permission->category) === 'user' ? 'selected' : '' }}>User Management</option>
                            <option value="role" {{ old('category', $permission->category) === 'role' ? 'selected' : '' }}>Role & Permission</option>
                            <option value="attendance" {{ old('category', $permission->category) === 'attendance' ? 'selected' : '' }}>Attendance</option>
                            <option value="academic" {{ old('category', $permission->category) === 'academic' ? 'selected' : '' }}>Academic</option>
                            <option value="fee" {{ old('category', $permission->category) === 'fee' ? 'selected' : '' }}>Fee Management</option>
                            <option value="report" {{ old('category', $permission->category) === 'report' ? 'selected' : '' }}>Reports</option>
                            <option value="setting" {{ old('category', $permission->category) === 'setting' ? 'selected' : '' }}>Settings</option>
                        </select>
                        @error('category')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                            id="description" name="description" rows="3"
                            placeholder="Brief description of this permission">{{ old('description', $permission->description) }}</textarea>
                        @error('description')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save" style="width: 14px;" class="me-1"></i>Update Permission
                        </button>
                        <a href="{{ route('admin.access-control.permissions.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="card-title fw-bold mb-0">Permission Details</h6>
            </div>
            <div class="card-body">
                <p class="text-sm mb-2">
                    <strong>Permission Name:</strong><br>
                    <code>{{ $permission->name }}</code>
                </p>
                <p class="text-sm mb-2">
                    <strong>Category:</strong><br>
                    <span class="badge bg-info">{{ ucfirst($permission->category) }}</span>
                </p>
                <p class="text-sm mb-2">
                    <strong>Assigned to Roles:</strong><br>
                    @if($permission->roles()->count() > 0)
                    <span class="badge bg-success">{{ $permission->roles()->count() }} role(s)</span>
                    @else
                    <span class="badge bg-secondary">Not assigned</span>
                    @endif
                </p>
                @if($permission->roles()->count() > 0)
                <hr>
                <p class="text-sm"><strong>Assigned Roles:</strong></p>
                <ul class="text-sm">
                    @foreach($permission->roles as $role)
                    <li><span class="badge bg-primary">{{ $role->name }}</span></li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection