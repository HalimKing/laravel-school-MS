@extends('layouts.app')

@section('title', 'User Role Management')

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">User Role Management</h6>
    </div>
</div>

@include('includes.message')

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="py-3">User Name</th>
                    <th class="py-3">Email</th>
                    <th class="py-3">Assigned Roles</th>
                    <th class="py-3" style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td class="py-3">{{ $user->name }}</td>
                    <td class="py-3">{{ $user->email }}</td>
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
                        <a href="{{ route('admin.management.users.assign-roles', $user->id) }}" class="btn btn-sm btn-outline-primary" title="Assign Roles">
                            <i data-lucide="edit" style="width: 14px;"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">
                        No users found.
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