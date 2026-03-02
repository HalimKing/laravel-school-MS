@extends('layouts.app')

@section('title', 'Permissions Management')

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">Permissions Management</h6>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.access-control.permissions.create') }}" class="btn btn-success btn-sm">
                <i data-lucide="plus" style="width: 14px;" class="me-1"></i>Add Permission
            </a>
        </div>
    </div>
</div>

@include('includes.message')

<div class="row">
    @forelse($permissions as $category => $permissionList)
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="card-title fw-bold mb-0 text-uppercase" style="font-size: 13px;">
                    {{ ucfirst(str_replace('_', ' ', $category)) }}
                    <span class="badge bg-primary float-end">{{ $permissionList->count() }}</span>
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <tbody>
                        @foreach($permissionList as $permission)
                        <tr>
                            <td class="py-2">
                                <code class="text-muted">{{ $permission->name }}</code>
                                @if($permission->description)
                                <br>
                                <small class="text-muted">{{ $permission->description }}</small>
                                @endif
                            </td>
                            <td class="py-2" style="width: 80px;">
                                <div class="btn-group" role="group" style="gap: 4px;">
                                    <a href="{{ route('admin.access-control.permissions.edit', $permission->id) }}"
                                        class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i data-lucide="edit" style="width: 14px;"></i>
                                    </a>
                                    <form action="{{ route('admin.access-control.permissions.destroy', $permission->id) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Are you sure?');" title="Delete">
                                            <i data-lucide="trash-2" style="width: 14px;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info">
            No permissions found. <a href="{{ route('admin.permissions.create') }}">Create one</a>
        </div>
    </div>
    @endforelse
</div>

@endsection