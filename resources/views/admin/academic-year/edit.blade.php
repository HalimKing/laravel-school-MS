@extends('layouts.app')

@section('title', 'Edit School Session')

@section('content')

<div class="card mb-4 p-4">
<h6 class="d-flex align-items-center mb-0">
<span class="text-muted">School Sessions</span>
<i data-lucide="chevron-right" class="mx-2" style="width: 16px;"></i>
<span>Edit Session</span>
<i data-lucide="chevron-right" class="mx-2" style="width: 16px;"></i>
<span class="text-primary">{{ $session->name }}</span>
</h6>
</div>

@include('includes.message')

<div class="row">
<div class="col-md-6 grid-margin stretch-card">
<div class="card">
<div class="card-body">
<div class="d-flex justify-content-between align-items-center mb-4">
<h6 class="card-title mb-0">Modify Session</h6>
<a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.sessions.index') }}">
<i data-lucide="arrow-left" class="me-1" style="width: 14px;"></i> Back
</a>
</div>

            <form action="{{ route('admin.sessions.update', $session->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="name" class="form-label">Session Name</label>
                    <input type="text" name="name" id="name" 
                           class="form-control @error('name') is-invalid @enderror" 
                           placeholder="e.g. 2023/2024" 
                           value="{{ old('name', $session->name) }}" required autofocus>
                    <div class="form-text">Update the name for this academic period.</div>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">
                        <i data-lucide="check-circle" class="me-1" style="width: 18px;"></i> Update Session
                    </button>
                    <a href="{{ route('admin.sessions.index') }}" class="btn btn-light ms-1">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="col-md-4">
    <div class="card border-0">
        <div class="card-body">
            <h6 class="card-title d-flex align-items-center">
                <i data-lucide="info" class="me-2 text-info" style="width: 18px;"></i> Session Info
            </h6>
            <p class="text-muted small">
                <strong>Created:</strong> {{ $session->created_at->format('M d, Y') }}<br>
                <strong>Last Update:</strong> {{ $session->updated_at->diffForHumans() }}
            </p>
            <hr>
            <p class="text-muted small">
                Changing the name will update all records associated with this session. If you change the status to <strong>Active</strong>, ensure other sessions are managed accordingly to avoid scheduling conflicts.
            </p>
        </div>
    </div>
</div>


</div>

@endsection