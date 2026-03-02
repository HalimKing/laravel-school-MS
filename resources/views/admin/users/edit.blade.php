@extends('layouts.app')

@section('title', 'Edit User: ' . $user->name)

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">Edit User: <span class="badge bg-primary">{{ $user->name }}</span></h6>
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
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                            id="name" name="name" placeholder="Enter full name"
                            value="{{ old('name', $user->name) }}" required>
                        @error('name')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                            id="email" name="email" placeholder="Enter email address"
                            value="{{ old('email', $user->email) }}" required>
                        @error('email')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold mb-3">Change Password (Optional)</h6>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold">New Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" name="password" placeholder="Leave blank to keep current password">
                        <small class="text-muted">Minimum 8 characters</small>
                        @error('password')
                        <small class="text-danger d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-bold">Confirm Password</label>
                        <input type="password" class="form-control"
                            id="password_confirmation" name="password_confirmation"
                            placeholder="Confirm password">
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold mb-3">Assign Roles</h6>

                    @if($roles->count() > 0)
                    <div class="row">
                        @foreach($roles as $role)
                        @if($role->id)
                        <div class="col-md-6 mb-2">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input"
                                    name="roles[]" value="{{ (int)$role->id }}"
                                    id="role_{{ $role->id }}"
                                    {{ in_array($role->id, $userRoles) ? 'checked' : '' }}>
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
                    <p class="text-muted">No roles available.</p>
                    @endif

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save" style="width: 14px;" class="me-1"></i>Update User
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-3">
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
                <p class="text-sm">
                    <strong>Joined:</strong><br>
                    <small class="text-muted">{{ $user->created_at->format('M d, Y') }}</small>
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-light">
                <h6 class="card-title fw-bold mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline-primary d-block mb-2">
                    <i data-lucide="eye" style="width: 14px;" class="me-1"></i>View Profile
                </a>
                @if(auth()->user()->id !== $user->id)
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline-block w-100">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger d-block w-100"
                        onclick="return confirm('Are you sure? This action cannot be undone.');">
                        <i data-lucide="trash-2" style="width: 14px;" class="me-1"></i>Delete User
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection