@extends('layouts.app')

@section('title', 'Create User')

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">Create New User</h6>
        <div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
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
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                            id="name" name="name" placeholder="Enter full name"
                            value="{{ old('name') }}" required>
                        @error('name')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                            id="email" name="email" placeholder="Enter email address"
                            value="{{ old('email') }}" required>
                        @error('email')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" name="password" placeholder="Enter password (minimum 8 characters)"
                            required>
                        @error('password')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label fw-bold">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control"
                            id="password_confirmation" name="password_confirmation"
                            placeholder="Confirm password" required>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-3 d-block">Assign Roles</label>

                        @if($roles->count() > 0)
                        <div class="row">
                            @foreach($roles as $role)
                            @if($role->id)
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input"
                                        name="roles[]" value="{{ (int)$role->id }}"
                                        id="role_{{ $role->id }}"
                                        {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="role_{{ $role->id }}">
                                        <strong>{{ $role->name }}</strong>
                                        @if($role->description)
                                        <br><small class="text-muted">{{ $role->description }}</small>
                                        @endif
                                    </label>
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                        @else
                        <p class="text-muted">No roles available. <a href="{{ route('admin.access-control.roles.create') }}">Create roles first</a></p>
                        @endif
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save" style="width: 14px;" class="me-1"></i>Create User
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
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
                    <strong>Create New User:</strong> Enter the user's details and select appropriate roles for access control.
                </p>
                <hr>
                <p class="text-sm"><strong>Password Requirements:</strong></p>
                <ul class="text-sm text-muted">
                    <li>Minimum 8 characters</li>
                    <li>Must match confirmation</li>
                    <li>Share securely with user</li>
                </ul>
                <hr>
                <p class="text-sm"><strong>Roles:</strong></p>
                <p class="text-sm text-muted">
                    Select one or more roles to define user permissions. Users inherit permissions from their assigned roles.
                </p>
            </div>
        </div>
    </div>
</div>

@endsection