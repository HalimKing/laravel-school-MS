@extends('layouts.app')

@section('title', 'Access Denied')

@section('content')
<div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 500px;">
    <div class="text-center">
        <h1 class="display-1 fw-bold text-danger mb-4">403</h1>
        <h2 class="mb-3">Access Denied</h2>
        <p class="text-muted mb-4">
            You don't have permission to access this resource.
        </p>

        <div class="alert alert-warning d-inline-block mb-4">
            <i data-lucide="alert-circle" style="width: 18px;" class="me-2"></i>
            <strong>Permission Required:</strong> Your current role and permissions do not allow you to access this page.
        </div>

        <div class="mb-4">
            <p class="text-muted small">
                Your assigned roles:
                @if(auth()->user()->roles->count() > 0)
                @foreach(auth()->user()->roles as $role)
                <span class="badge bg-primary">{{ $role->name }}</span>
                @endforeach
                @else
                <span class="badge bg-secondary">No roles assigned</span>
                @endif
            </p>
        </div>

        <div class="d-flex gap-2 justify-content-center">
            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                <i data-lucide="home" style="width: 14px;" class="me-1"></i>
                Back to Dashboard
            </a>
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                <i data-lucide="arrow-left" style="width: 14px;" class="me-1"></i>
                Go Back
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endsection