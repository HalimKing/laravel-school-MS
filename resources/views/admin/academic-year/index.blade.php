@extends('layouts.app')

@section('title', 'School Sessions')

@section('content')
<div class="card mb-4 p-4">
<h6 class="d-flex align-items-center mb-0">
<span>School Sessions</span>
</h6>
</div>

@include('includes.message')

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title mb-0">Session List</h6>
                    <a class="btn btn-primary" href="{{ route('admin.sessions.create') }}"> 
                        <i data-lucide="plus" class="me-1" style="width: 18px;"></i> Add Session
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table id="dataTableExample" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessions as $session)
                                <tr>
                                    <td class="align-middle">
                                        <span class="fw-bold">{{ $session->name }}</span>
                                    </td>
                                    <td class="align-middle">
                                        @if($session->status == 'active')
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end align-middle">
                                        <div class="dropdown">
                                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.sessions.edit', $session->id) }}">
                                                        <i data-lucide="pencil" class="me-2" style="width: 14px;"></i> Edit
                                                    </a>
                                                </li>
                                                <!-- activate session to use post method -->
                                               
                                                <li>
                                                    <form method="POST" action="{{ route('admin.sessions.activate', $session->id) }}" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="dropdown-item d-flex align-items-center" onclick="return confirm('Activate this session?')">
                                                            <i data-lucide="check" class="me-2" style="width: 14px;"></i> Activate
                                                        </button>
                                                    </form>
                                                </li>
                                                
                                                

                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <form method="POST" action="{{ route('admin.sessions.destroy', $session->id) }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger d-flex align-items-center" onclick="return confirm('Delete this session permanently?')">
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
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        No school sessions found.
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