@extends('layouts.app')

@section('title', 'Academic Periods')

@section('content')
<div class="card mb-4 p-4">
<h6 class="mb-0 text-uppercase fw-bold">Academic Periods</h6>
</div>

@include('includes.message')

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title mb-0">Period List</h6>
                    <a class="btn btn-primary" href="{{ route('admin.academics.academic-periods.create') }}"> 
                        <i data-lucide="plus" class="me-1" style="width: 18px;"></i> Add Period
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table id="dataTableExample" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($academicPeriods as $period)
                                <tr>
                                    <td class="align-middle">
                                        <span class="fw-semibold">{{ $period->name }}</span>
                                    </td>
                                    <td class="text-end align-middle">
                                        <div class="dropdown">
                                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.academics.academic-periods.edit', $period->id) }}">
                                                        <i data-lucide="pencil" class="me-2 text-muted" style="width: 14px;"></i> Edit
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form method="POST" action="{{ route('admin.academics.academic-periods.destroy', $period->id) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger d-flex align-items-center" onclick="return confirm('Delete this academic period permanently?')">
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
                                    <td colspan="2" class="text-center py-5 text-muted">
                                        <i data-lucide="calendar" class="mb-2" style="width: 40px; height: 40px; opacity: 0.2;"></i>
                                        <p class="mb-0">No academic periods found.</p>
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