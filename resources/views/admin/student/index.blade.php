@extends('layouts.app')

@section('title', 'All Students')

@section('content')
<div class="card mb-4 p-4">
<h6 class="mb-0 text-uppercase fw-bold">Student Management</h6>
</div>

@include('includes.message')

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title mb-0">Student List</h6>
                    <a class="btn btn-primary" href="{{ route('admin.students.create') }}"> 
                        <i data-lucide="user-plus" class="me-1" style="width: 18px;"></i> Add Student
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table id="dataTableExample" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Student Details</th>
                                <th>Date of Birth</th>
                                <th>Class</th>
                                <th>Gender</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                <tr>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <i data-lucide="graduation-cap" class="text-secondary" style="width: 20px;"></i>
                                            </div>
                                            <div>
                                                <span class="fw-bold d-block">{{ $student->first_name }} {{ $student->last_name }}</span>
                                                <small class="text-muted">{{ $student->student_id ?? 'No ID' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge text-primary border">{{ $student->date_of_birth }}</span>
                                    </td>
                                    <td class="align-middle">
                                        {{ $student->latestLevel->class->name ?? 'Not Assigned' }}
                                    </td>
                                    <td class="align-middle text-capitalize">
                                        {{ $student->gender }}
                                    </td>
                                    <td class="align-middle">
                                        @if($student->status == 'active')
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end align-middle">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.students.show', $student->id) }}">
                                                        <i data-lucide="eye" class="me-2 text-muted" style="width: 14px;"></i> View Profile
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.students.edit', $student->id) }}">
                                                        <i data-lucide="pencil" class="me-2 text-muted" style="width: 14px;"></i> Edit
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form method="POST" action="{{ route('admin.students.destroy', $student->id) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger d-flex align-items-center" onclick="return confirm('Are you sure you want to delete this student record?')">
                                                            <i data-lucide="trash-2" class="me-2" style="width: 14px;"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i data-lucide="users" class="mb-2" style="width: 40px; height: 40px; opacity: 0.2;"></i>
                                        <p class="mb-0">No student records found.</p>
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