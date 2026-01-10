@extends('layouts.app')

@section('title', 'Fee Categories')

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">Fee Categories</h6>
        <a class="btn btn-primary btn-sm d-flex align-items-center" href="{{ route('admin.fee-management.fee-categories.create') }}">
            <i data-lucide="plus" class="me-1" style="width: 16px;"></i> Add New Category
        </a>
    </div>
</div>

@include('includes.message')

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dataTableExample" class="table table-hover align-middle">
                        <thead class="">
                            <tr>
                                <th class="py-3" style="width: 30%;">Name</th>
                                <th class="py-3" style="width: 50%;">Description</th>
                                <th class="py-3 text-end" style="width: 20%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($feeCategories as $category)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary-subtle text-primary rounded p-2 me-3">
                                            <i data-lucide="wallet" style="width: 18px;"></i>
                                        </div>
                                        <span class="fw-bold">{{ $category->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        {{ $category->description ?? 'No description provided.' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-primary btn-sm dropdown-toggle border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.fee-management.fee-categories.edit', $category->id) }}">
                                                    <i data-lucide="pencil" class="me-2 text-muted" style="width: 14px;"></i> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <form method="POST" action="{{ route('admin.fee-management.fee-categories.destroy', $category->id) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger d-flex align-items-center" onclick="return confirm('Are you sure you want to delete this category? This may affect linked fee structures.')">
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
                                <td colspan="3" class="text-center py-5 text-muted">
                                    <div class="mb-3">
                                        <i data-lucide="layers" class="opacity-25" style="width: 48px; height: 48px;"></i>
                                    </div>
                                    <p class="mb-0">No fee categories found.</p>
                                    <small>Create categories like "Tuition Fees", "Library", or "Transport".</small>
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