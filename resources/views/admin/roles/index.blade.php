@extends('layouts.app')

@section('title', 'Roles Management')

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">Roles Management</h6>
        <div>
            <a href="{{ route('admin.access-control.roles.create') }}" class="btn btn-success btn-sm">
                <i data-lucide="plus" style="width: 14px;" class="me-1"></i>Add Role
            </a>
        </div>
    </div>
</div>

@include('includes.message')

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="py-3">Role Name</th>
                    <th class="py-3">Description</th>
                    <th class="py-3">Permissions</th>
                    <th class="py-3">Users</th>
                    <th class="py-3" style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                <tr>
                    <td class="py-3">
                        <span class="badge bg-primary">{{ $role->name }}</span>
                    </td>
                    <td class="py-3">
                        <small class="text-muted">{{ $role->description ?? 'N/A' }}</small>
                    </td>
                    <td class="py-3">
                        <small>
                            @if($role->permissions->count() > 0)
                            <span class="badge bg-info">{{ $role->permissions->count() }} permissions</span>
                            @else
                            <span class="badge bg-secondary">No permissions</span>
                            @endif
                        </small>
                    </td>
                    <td class="py-3">
                        <small class="text-muted">{{ $role->users()->count() }} user(s)</small>
                    </td>
                    <td class="py-3">
                        <a href="{{ route('admin.access-control.roles.edit', $role->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                            <i data-lucide="edit" style="width: 14px;"></i>
                        </a>
                        @if($role->name !== 'super-admin')
                        <form action="{{ route('admin.access-control.roles.destroy', $role->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?');" title="Delete">
                                <i data-lucide="trash-2" style="width: 14px;"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        No roles found. <a href="{{ route('admin.access-control.roles.create') }}">Create one</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $roles->links() }}
</div>

@endsection