@extends('layouts.app')

@section('title', 'Assign Roles: ' . $user->name)

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">Assign Roles: <span class="badge bg-primary">{{ $user->name }}</span></h6>
        <div>
            <a href="{{ route('admin.management.users.roles') }}" class="btn btn-outline-secondary btn-sm">
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
                <form action="{{ route('admin.management.users.update-roles', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="fw-bold mb-3 d-block">Select Roles</label>

                        @if($roles->count() > 0)
                        <div class="row">
                            @foreach($roles as $role)
                            <div class="col-md-6 mb-3">
                                <div class="card border {{ in_array($role->id, $userRoles) ? 'border-primary bg-light' : '' }}">
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input"
                                                name="roles[]" value="{{ $role->id }}"
                                                id="role_{{ $role->id }}"
                                                {{ in_array($role->id, $userRoles) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="role_{{ $role->id }}">
                                                <strong>{{ $role->name }}</strong>
                                            </label>
                                        </div>
                                        @if($role->description)
                                        <p class="text-muted text-sm mt-2 mb-0">{{ $role->description }}</p>
                                        @endif
                                        <p class="text-muted text-sm mt-2 mb-0">
                                            <strong>Permissions:</strong> {{ $role->permissions->count() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="alert alert-warning">
                            No roles available. <a href="{{ route('admin.access-control.roles.create') }}">Create roles first</a>
                        </div>
                        @endif
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save" style="width: 14px;" class="me-1"></i>Update Roles
                        </button>
                        <a href="{{ route('admin.management.users.roles') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="card-title fw-bold mb-0">User Information</h6>
            </div>
            <div class="card-body">
                <p class="text-sm mb-2">
                    <strong>Name:</strong><br>
                    {{ $user->name }}
                </p>
                <p class="text-sm mb-2">
                    <strong>Email:</strong><br>
                    {{ $user->email }}
                </p>
                <p class="text-sm mb-2">
                    <strong>Current Roles:</strong><br>
                    @if($user->roles->count() > 0)
                    @foreach($user->roles as $role)
                    <span class="badge bg-primary">{{ $role->name }}</span>
                    @endforeach
                    @else
                    <span class="text-muted">No roles assigned</span>
                    @endif
                </p>
                <hr>
                <p class="text-sm">
                    <strong>Permissions:</strong><br>
                    <small class="text-muted">
                        {{ $user->getAllPermissions()->count() }} permission(s) from assigned roles
                    </small>
                </p>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-light">
                <h6 class="card-title fw-bold mb-0">Direct Permissions</h6>
            </div>
            <div class="card-body">
                @if($user->getDirectPermissions()->count() > 0)
                <ul class="text-sm list-unstyled">
                    @foreach($user->getDirectPermissions() as $permission)
                    <li><code>{{ $permission->name }}</code></li>
                    @endforeach
                </ul>
                @else
                <p class="text-muted text-sm">No direct permissions assigned</p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection