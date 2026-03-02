@extends('layouts.app')

@section('title', $announcement->title)

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">{{ $announcement->title }}</h6>
        <div>
            <a href="{{ route('announcements.edit', $announcement) }}" class="btn btn-warning btn-sm me-2">
                <i data-lucide="edit" style="width: 14px;" class="me-1"></i>Edit
            </a>
            <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" style="display: inline;">
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
                        @if($announcement->category)
                        <span class="badge bg-info">{{ ucfirst($announcement->category) }}</span>
                        @else
                        <span class="badge bg-secondary">General</span>
                        @endif
                    </p>
                </div>

                <div class="mb-3">
                    <span class="text-muted small fw-bold">PRIORITY</span>
                    <p class="mb-0">
                        @if($announcement->priority == 'high')
                        <span class="badge bg-danger">High</span>
                        @elseif($announcement->priority == 'normal')
                        <span class="badge bg-warning">Normal</span>
                        @else
                        <span class="badge bg-secondary">Low</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <span class="text-muted small fw-bold">STATUS</span>
                    <p class="mb-0">
                        @if($announcement->status == 'published')
                        <span class="badge bg-success">Published</span>
                        @elseif($announcement->status == 'draft')
                        <span class="badge bg-warning">Draft</span>
                        @else
                        <span class="badge bg-secondary">Archived</span>
                        @endif
                    </p>
                </div>

                <div class="mb-3">
                    <span class="text-muted small fw-bold">CREATED BY</span>
                    <p class="mb-0">{{ $announcement->user->name ?? 'System' }}</p>
                </div>
            </div>
        </div>

        <hr>

        <div class="mb-4">
            <span class="text-muted small fw-bold">DESCRIPTION</span>
            <p>{{ $announcement->description }}</p>
        </div>

        <hr>

        <div class="row text-muted small">
            <div class="col-md-6">
                <p><strong>Created:</strong> {{ is_string($announcement->created_at) ? \Carbon\Carbon::parse($announcement->created_at)->format('M d, Y \a\t H:i') : $announcement->created_at->format('M d, Y \a\t H:i') }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Last Updated:</strong> {{ is_string($announcement->updated_at) ? \Carbon\Carbon::parse($announcement->updated_at)->format('M d, Y \a\t H:i') : $announcement->updated_at->format('M d, Y \a\t H:i') }}</p>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('announcements.index') }}" class="btn btn-secondary">
        <i data-lucide="arrow-left" style="width: 14px;" class="me-1"></i>Back to Announcements
    </a>
</div>

@endsection