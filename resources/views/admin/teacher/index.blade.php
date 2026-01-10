@extends('layouts.app')

@section('title', 'All Teachers')

@section('content')
<div class="card mb-4 p-4">
<h6 class="mb-0 text-uppercase fw-bold">Teachers Management</h6>
</div>

@include('includes.message')

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title mb-0">Teacher List</h6>
                    <a class="btn btn-primary" href="{{ route('admin.teachers.create') }}"> 
                        <i data-lucide="user-plus" class="me-1" style="width: 18px;"></i> Add Teacher
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table id="dataTableExample" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Teacher Details</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teachers as $teacher)
                                <tr>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <i data-lucide="user" class="text-secondary" style="width: 20px;"></i>
                                            </div>
                                            <div>
                                                <span class="fw-bold d-block">{{ $teacher->first_name }} {{ $teacher->last_name }}</span>
                                                <small class="text-muted">ID: {{ $teacher->staff_id ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">{{ $teacher->email }}</td>
                                    <td class="align-middle">{{ $teacher->phone ?? '-' }}</td>
                                    <td class="align-middle">
                                        @if($teacher->status == 'active')
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end align-middle">
                                        <div class="dropdown">
                                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.teachers.show', $teacher->id) }}">
                                                        <i data-lucide="eye" class="me-2 text-muted" style="width: 14px;"></i> View Profile
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.teachers.edit', $teacher->id) }}">
                                                        <i data-lucide="pencil" class="me-2 text-muted" style="width: 14px;"></i> Edit
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form method="POST" action="{{ route('admin.teachers.destroy', $teacher->id) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger d-flex align-items-center" onclick="return confirm('Are you sure you want to remove this teacher?')">
                                                            <i data-lucide="user-minus" class="me-2" style="width: 14px;"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i data-lucide="users" class="mb-2" style="width: 40px; height: 40px; opacity: 0.2;"></i>
                                        <p class="mb-0">No teachers found in the records.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection