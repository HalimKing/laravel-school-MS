@extends('layouts.app')

@section('title', 'User: ' . $user->name)

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">User Profile: <span class="badge bg-primary">{{ $user->name }}</span></h6>
        <div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                <i data-lucide="arrow-left" style="width: 14px;" class="me-1"></i>Back
            </a>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i data-lucide="check-circle" style="width: 16px;" class="me-2"></i>
    <strong>Success!</strong> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="card-title fw-bold mb-0">User Details</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <p class="text-muted small mb-1">Full Name</p>
                        <h6 class="fw-bold">{{ $user->name }}</h6>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p class="text-muted small mb-1">Email Address</p>
                        <h6 class="fw-bold">{{ $user->email }}</h6>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p class="text-muted small mb-1">Account Status</p>
                        <div>
                            @if($user->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-danger">Inactive</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p class="text-muted small mb-1">Member Since</p>
                        <h6 class="fw-bold">{{ $user->created_at->format('M d, Y') }}</h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted small mb-1">Last Updated</p>
                        <h6 class="fw-bold">{{ $user->updated_at->format('M d, Y H:i') }}</h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="card-title fw-bold mb-0">Assigned Roles</h6>
            </div>
            <div class="card-body">
                @if($user->roles->count() > 0)
                <div class="row">
                    @foreach($user->roles as $role)
                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light">
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-2">{{ $role->name }}</h6>
                                    @if($role->description)
                                    <p class="text-muted small mb-0">{{ $role->description }}</p>
                                    @endif
                                </div>
                                <span class="badge bg-primary ms-2">Role</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted">
                    <i data-lucide="alert-circle" style="width: 16px;" class="me-1"></i>
                    No roles assigned to this user.
                </p>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-light">
                <h6 class="card-title fw-bold mb-0">Inherited Permissions</h6>
            </div>
            <div class="card-body">
                @if($permissions && $permissions->count() > 0)
                <p class="text-muted small mb-3">
                    Total permissions: <strong>{{ $permissions->count() }}</strong>
                </p>
                <div class="row">
                    @forelse($permissions->groupBy(function($item) {
                    return !empty($item->name) ? explode('.', $item->name)[0] : 'other';
                    }) as $category => $perms)
                    <div class="col-md-6 mb-4">
                        <h6 class="fw-bold text-capitalize mb-3">
                            <i data-lucide="lock" style="width: 16px;" class="me-2"></i>
                            {{ str_replace('_', ' ', $category) }}
                        </h6>
                        <div class="permission-list">
                            @foreach($perms as $perm)
                            @if($perm && !empty($perm->name))
                            <div class="mb-2">
                                <small class="text-muted">✓ {{ $perm->name }}</small>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <p class="text-muted">No permissions</p>
                    @endforelse
                </div>
                @else
                <p class="text-muted">
                    <i data-lucide="alert-circle" style="width: 16px;" class="me-1"></i>
                    No permissions available. Assign roles to grant permissions.
                </p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="card-title fw-bold mb-0">Account Actions</h6>
            </div>
            <div class="card-body d-flex flex-column gap-2">
                @can('user.update')
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-primary">
                    <i data-lucide="edit" style="width: 14px;" class="me-1"></i>Edit User
                </a>
                @endcan

                @can('user.delete')
                @if(auth()->check() && auth()->user()->id !== $user->id)
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline-block w-100">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger w-100"
                        onclick="return confirm('Are you sure? This action cannot be undone.');">
                        <i data-lucide="trash-2" style="width: 14px;" class="me-1"></i>Delete User
                    </button>
                </form>
                @else
                <button type="button" class="btn btn-sm btn-outline-secondary w-100" disabled title="You cannot delete your own account">
                    <i data-lucide="trash-2" style="width: 14px;" class="me-1"></i>Delete User
                </button>
                @endif
                @endcan

                @can('user.update')
                @if($user->is_active)
                <form action="{{ route('admin.users.toggle-active', $user->id) }}" method="POST" class="d-inline-block w-100">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-warning w-100"
                        onclick="return confirm('Deactivate this user?');">
                        <i data-lucide="user-x" style="width: 14px;" class="me-1"></i>Deactivate User
                    </button>
                </form>
                @else
                <form action="{{ route('admin.users.toggle-active', $user->id) }}" method="POST" class="d-inline-block w-100">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-success w-100">
                        <i data-lucide="user-check" style="width: 14px;" class="me-1"></i>Activate User
                    </button>
                </form>
                @endif
                @endcan
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="card-title fw-bold mb-0">Statistics</h6>
            </div>
            <div class="card-body">
                <p class="text-sm mb-2">
                    <strong>Total Roles:</strong><br>
                    <span class="badge bg-info">{{ $user->roles->count() }}</span>
                </p>
                <p class="text-sm mb-2">
                    <strong>Total Permissions:</strong><br>
                    <span class="badge bg-warning">{{ $permissions->count() }}</span>
                </p>
                <p class="text-sm">
                    <strong>Account Status:</strong><br>
                    @if($user->is_active)
                    <span class="badge bg-success">Active</span>
                    @else
                    <span class="badge bg-danger">Inactive</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="alert alert-info small">
            <i data-lucide="info" style="width: 14px;" class="me-1"></i>
            <strong>Note:</strong> Permissions are automatically granted through assigned roles. To change permissions, assign or remove roles.
        </div>
    </div>
</div>

@endsection