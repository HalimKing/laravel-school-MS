@extends('layouts.app')

@section('title', 'User Management')

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">User Management</h6>
        <div>
            @can('user.create')
            <a href="{{ route('admin.users.create') }}" class="btn btn-success btn-sm">
                <i data-lucide="plus" style="width: 14px;" class="me-1"></i>Add User
            </a>
            @endcan
        </div>
    </div>
</div>

@include('includes.message')

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="py-3">Name</th>
                    <th class="py-3">Email</th>
                    <th class="py-3">Roles</th>
                    <th class="py-3">Status</th>
                    <th class="py-3" style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td class="py-3">
                        <strong>{{ $user->name }}</strong>
                    </td>
                    <td class="py-3">
                        <small class="text-muted">{{ $user->email }}</small>
                    </td>
                    <td class="py-3">
                        @if($user->roles->count() > 0)
                        @foreach($user->roles as $role)
                        <span class="badge bg-primary">{{ $role->name }}</span>
                        @endforeach
                        @else
                        <span class="badge bg-secondary">No roles</span>
                        @endif
                    </td>
                    <td class="py-3">
                        @if($user->is_active ?? true)
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-danger">Inactive</span>
                        @endif
                    </td>
                    <td class="py-3">
                        <div class="btn-group" role="group" style="gap: 4px;">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline-primary" title="View">
                                <i data-lucide="eye" style="width: 14px;"></i>
                            </a>
                            @can('user.update')
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                <i data-lucide="edit" style="width: 14px;"></i>
                            </a>
                            @endcan
                            @can('user.delete')
                            @if(auth()->check() && auth()->user()->id !== $user->id)
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?');" title="Delete">
                                    <i data-lucide="trash-2" style="width: 14px;"></i>
                                </button>
                            </form>
                            @endif
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        No users found. <a href="{{ route('admin.users.create') }}">Create one</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $users->links() }}
</div>

@endsection