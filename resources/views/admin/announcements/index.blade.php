@extends('layouts.app')

@section('title', 'Announcements')

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">Announcements</h6>
        <a href="{{ route('announcements.create') }}" class="btn btn-primary btn-sm">
            <i data-lucide="plus" style="width: 14px;" class="me-1"></i>New Announcement
        </a>
    </div>
</div>

@include('includes.message')

<div class="card mb-4">
    <div class="card-body">
        <h6 class="card-title fw-bold mb-4">Filters</h6>

        <form method="GET" action="{{ route('announcements.index') }}" class="filter-form">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Search</label>
                    <input type="text" name="search" class="form-control"
                        placeholder="Search by title or description..."
                        value="{{ request('search') }}">
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Priority</label>
                    <select name="priority" class="form-select">
                        <option value="">All Priorities</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Category</label>
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                            {{ ucfirst($cat) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                        <i data-lucide="search" style="width: 14px;" class="me-1"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th width="35%">
                        <a href="{{ route('announcements.index', array_merge(request()->query(), ['sort_by' => 'title', 'sort_order' => request('sort_order') == 'asc' && request('sort_by') == 'title' ? 'desc' : 'asc'])) }}">
                            Title
                            @if(request('sort_by') == 'title')
                            <i data-lucide="{{ request('sort_order') == 'asc' ? 'arrow-up' : 'arrow-down' }}" style="width: 14px;"></i>
                            @endif
                        </a>
                    </th>
                    <th width="15%">Category</th>
                    <th width="10%">Priority</th>
                    <th width="12%">
                        <a href="{{ route('announcements.index', array_merge(request()->query(), ['sort_by' => 'created_at', 'sort_order' => request('sort_order') == 'asc' && request('sort_by') == 'created_at' ? 'desc' : 'asc'])) }}">
                            Date
                            @if(request('sort_by') == 'created_at')
                            <i data-lucide="{{ request('sort_order') == 'asc' ? 'arrow-up' : 'arrow-down' }}" style="width: 14px;"></i>
                            @endif
                        </a>
                    </th>
                    <th width="10%">Status</th>
                    <th width="18%">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($announcements as $announcement)
                <tr>
                    <td>
                        <a href="{{ route('announcements.show', $announcement) }}" class="text-decoration-none fw-bold">
                            {{ Str::limit($announcement->title, 40) }}
                        </a>
                    </td>
                    <td><span class="badge bg-info">{{ ucfirst($announcement->category ?? 'General') }}</span></td>
                    <td>
                        @if($announcement->priority == 'high')
                        <span class="badge bg-danger">High</span>
                        @elseif($announcement->priority == 'normal')
                        <span class="badge bg-warning">Normal</span>
                        @else
                        <span class="badge bg-secondary">Low</span>
                        @endif
                    </td>
                    <td>{{ is_string($announcement->created_at) ? \Carbon\Carbon::parse($announcement->created_at)->format('M d, Y') : $announcement->created_at->format('M d, Y') }}</td>
                    <td>
                        @if($announcement->status == 'published')
                        <span class="badge bg-success">Published</span>
                        @elseif($announcement->status == 'draft')
                        <span class="badge bg-warning">Draft</span>
                        @else
                        <span class="badge bg-secondary">Archived</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('announcements.show', $announcement) }}" class="btn btn-sm btn-outline-primary me-1" title="View">
                            <i data-lucide="eye" style="width: 14px;"></i>
                        </a>
                        <a href="{{ route('announcements.edit', $announcement) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit">
                            <i data-lucide="edit" style="width: 14px;"></i>
                        </a>
                        <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"
                                onclick="return confirm('Are you sure?')">
                                <i data-lucide="trash" style="width: 14px;"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        No announcements found. <a href="{{ route('announcements.create') }}">Create one</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $announcements->links() }}
</div>

@endsection