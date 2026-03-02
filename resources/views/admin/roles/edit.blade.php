@extends('layouts.app')

@section('title', 'Edit Role: ' . $role->name)

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">Edit Role: <span class="badge bg-primary">{{ $role->name }}</span></h6>
        <div>
            <a href="{{ route('admin.access-control.roles.index') }}" class="btn btn-outline-secondary btn-sm">
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
                <form action="{{ route('admin.access-control.roles.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Role Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                            id="name" name="name" placeholder="e.g., teacher, student, admin"
                            value="{{ old('name', $role->name) }}" required>
                        @error('name')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                            id="description" name="description" rows="3"
                            placeholder="Brief description of the role">{{ old('description', $role->description) }}</textarea>
                        @error('description')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-3 d-block">Assign Permissions</label>

                        @forelse($permissions as $category => $permissionList)
                        <div class="card mb-3 bg-light">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 fw-bold text-uppercase" style="font-size: 12px;">
                                    <input type="checkbox" class="category-checkbox" data-category="{{ $category }}"
                                        {{ $permissionList->whereIn('id', $rolePermissions)->count() === $permissionList->count() ? 'checked' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $category)) }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($permissionList as $permission)
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input category-{{ $category }}"
                                                name="permissions[]" value="{{ $permission->id }}"
                                                id="permission_{{ $permission->id }}"
                                                {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted">No permissions available.</p>
                        @endforelse
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save" style="width: 14px;" class="me-1"></i>Update Role
                        </button>
                        <a href="{{ route('admin.access-control.roles.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="card-title fw-bold mb-0">Role Details</h6>
            </div>
            <div class="card-body">
                <p class="text-sm text-muted mb-2">
                    <strong>Users assigned:</strong> <span class="badge bg-info">{{ $role->users()->count() }}</span>
                </p>
                <p class="text-sm text-muted mb-2">
                    <strong>Permissions:</strong> <span class="badge bg-info">{{ $role->permissions()->count() }}</span>
                </p>
                <hr>
                <p class="text-sm"><strong>Assigned Users:</strong></p>
                @if($role->users()->count() > 0)
                <ul class="text-sm">
                    @foreach($role->users as $user)
                    <li>{{ $user->name }} ({{ $user->email }})</li>
                    @endforeach
                </ul>
                @else
                <p class="text-muted text-sm">No users assigned to this role</p>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.category-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const category = this.dataset.category;
            const isChecked = this.checked;
            document.querySelectorAll(`.category-${category}`).forEach(perm => {
                perm.checked = isChecked;
            });
        });
    });
</script>
@endpush

@endsection