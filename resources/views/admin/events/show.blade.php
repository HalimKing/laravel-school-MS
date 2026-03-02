@extends('layouts.app')

@section('title', $event->title)

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">{{ $event->title }}</h6>
        <div>
            <a href="{{ route('events.edit', $event) }}" class="btn btn-warning btn-sm me-2">
                <i data-lucide="edit" style="width: 14px;" class="me-1"></i>Edit
            </a>
            <form action="{{ route('events.destroy', $event) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                    <i data-lucide="trash" style="width: 14px;" class="me-1"></i>Delete
                </button>
            </form>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <span class="text-muted small fw-bold">CATEGORY</span>
                    <p class="mb-0">
                        @if($event->category)
                        <span class="badge bg-info">{{ ucfirst($event->category) }}</span>
                        @else
                        <span class="badge bg-secondary">General</span>
                        @endif
                    </p>
                </div>

                <div class="mb-3">
                    <span class="text-muted small fw-bold">LOCATION</span>
                    <p class="mb-0">{{ $event->location ?? 'Not specified' }}</p>
                </div>

                <div class="mb-3">
                    <span class="text-muted small fw-bold">ATTENDANCE</span>
                    <p class="mb-0">{{ $event->attendance_count ?? 0 }} people</p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <span class="text-muted small fw-bold">STATUS</span>
                    <p class="mb-0">
                        @if($event->status == 'published')
                        <span class="badge bg-success">Published</span>
                        @elseif($event->status == 'draft')
                        <span class="badge bg-warning">Draft</span>
                        @elseif($event->status == 'cancelled')
                        <span class="badge bg-danger">Cancelled</span>
                        @else
                        <span class="badge bg-secondary">Completed</span>
                        @endif
                    </p>
                </div>

                <div class="mb-3">
                    <span class="text-muted small fw-bold">CREATED BY</span>
                    <p class="mb-0">{{ $event->user->name ?? 'System' }}</p>
                </div>
            </div>
        </div>

        <hr>

        <div class="row mb-4">
            <div class="col-md-6">
                <h6 class="fw-bold mb-3">Event Date & Time</h6>
                <p class="mb-2">
                    <strong>Start Date:</strong> {{ is_string($event->event_date) ? \Carbon\Carbon::parse($event->event_date)->format('F d, Y') : $event->event_date->format('F d, Y') }}
                    @if($event->start_time)
                    at {{ $event->start_time }}
                    @endif
                </p>
                @if($event->event_end_date)
                <p class="mb-2">
                    <strong>End Date:</strong> {{ is_string($event->event_end_date) ? \Carbon\Carbon::parse($event->event_end_date)->format('F d, Y') : $event->event_end_date->format('F d, Y') }}
                    @if($event->end_time)
                    at {{ $event->end_time }}
                    @endif
                </p>
                @endif
            </div>
        </div>

        <hr>

        <div class="mb-4">
            <span class="text-muted small fw-bold">DESCRIPTION</span>
            <p>{{ $event->description }}</p>
        </div>

        @if($event->notes)
        <hr>
        <div class="mb-4">
            <span class="text-muted small fw-bold">NOTES</span>
            <p>{{ $event->notes }}</p>
        </div>
        @endif

        <hr>

        <div class="row text-muted small">
            <div class="col-md-6">
                <p><strong>Created:</strong> {{ is_string($event->created_at) ? \Carbon\Carbon::parse($event->created_at)->format('M d, Y \a\t H:i') : $event->created_at->format('M d, Y \a\t H:i') }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Last Updated:</strong> {{ is_string($event->updated_at) ? \Carbon\Carbon::parse($event->updated_at)->format('M d, Y \a\t H:i') : $event->updated_at->format('M d, Y \a\t H:i') }}</p>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('events.index') }}" class="btn btn-secondary">
        <i data-lucide="arrow-left" style="width: 14px;" class="me-1"></i>Back to Events
    </a>
</div>

@endsection