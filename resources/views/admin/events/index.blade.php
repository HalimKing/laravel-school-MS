@extends('layouts.app')

@section('title', 'Events')

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">Events</h6>
        <a href="{{ route('events.create') }}" class="btn btn-primary btn-sm">
            <i data-lucide="plus" style="width: 14px;" class="me-1"></i>New Event
        </a>
    </div>
</div>

@include('includes.message')

<div class="card mb-4">
    <div class="card-body">
        <h6 class="card-title fw-bold mb-4">Filters</h6>

        <form method="GET" action="{{ route('events.index') }}" class="filter-form">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Search</label>
                    <input type="text" name="search" class="form-control"
                        placeholder="Search by title or location..."
                        value="{{ request('search') }}">
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Event Type</label>
                    <select name="event_type" class="form-select">
                        <option value="">All Events</option>
                        <option value="upcoming" {{ request('event_type') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="past" {{ request('event_type') == 'past' ? 'selected' : '' }}>Past</option>
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
                    <th width="30%">
                        <a href="{{ route('events.index', array_merge(request()->query(), ['sort_by' => 'title', 'sort_order' => request('sort_order') == 'asc' && request('sort_by') == 'title' ? 'desc' : 'asc'])) }}">
                            Title
                            @if(request('sort_by') == 'title')
                            <i data-lucide="{{ request('sort_order') == 'asc' ? 'arrow-up' : 'arrow-down' }}" style="width: 14px;"></i>
                            @endif
                        </a>
                    </th>
                    <th width="15%">Category</th>
                    <th width="15%">
                        <a href="{{ route('events.index', array_merge(request()->query(), ['sort_by' => 'event_date', 'sort_order' => request('sort_order') == 'asc' && request('sort_by') == 'event_date' ? 'desc' : 'asc'])) }}">
                            Event Date
                            @if(request('sort_by') == 'event_date')
                            <i data-lucide="{{ request('sort_order') == 'asc' ? 'arrow-up' : 'arrow-down' }}" style="width: 14px;"></i>
                            @endif
                        </a>
                    </th>
                    <th width="15%">Location</th>
                    <th width="10%">Status</th>
                    <th width="15%">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                <tr>
                    <td>
                        <a href="{{ route('events.show', $event) }}" class="text-decoration-none fw-bold">
                            {{ Str::limit($event->title, 35) }}
                        </a>
                    </td>
                    <td><span class="badge bg-info">{{ ucfirst($event->category ?? 'General') }}</span></td>
                    <td>{{ is_string($event->event_date) ? \Carbon\Carbon::parse($event->event_date)->format('M d, Y') : $event->event_date->format('M d, Y') }}</td>
                    <td>{{ Str::limit($event->location ?? 'N/A', 15) }}</td>
                    <td>
                        @if($event->status == 'published')
                        <span class="badge bg-success">Published</span>
                        @elseif($event->status == 'draft')
                        <span class="badge bg-warning">Draft</span>
                        @elseif($event->status == 'cancelled')
                        <span class="badge bg-danger">Cancelled</span>
                        @else
                        <span class="badge bg-secondary">Completed</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('events.show', $event) }}" class="btn btn-sm btn-outline-primary me-1" title="View">
                            <i data-lucide="eye" style="width: 14px;"></i>
                        </a>
                        <a href="{{ route('events.edit', $event) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit">
                            <i data-lucide="edit" style="width: 14px;"></i>
                        </a>
                        <form action="{{ route('events.destroy', $event) }}" method="POST" style="display: inline;">
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
                        No events found. <a href="{{ route('events.create') }}">Create one</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $events->links() }}
</div>

@endsection