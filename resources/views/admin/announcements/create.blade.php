@extends('layouts.app')

@section('title', 'Create Announcement')

@section('content')

<div class="card mb-4 p-4">
    <h6 class="mb-0 text-uppercase fw-bold">Create New Announcement</h6>
</div>

@include('includes.message')

<div class="card">
    <div class="card-body">
        <form action="{{ route('announcements.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                        <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
                            placeholder="Enter announcement title" value="{{ old('title') }}" required>
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                        <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                            rows="6" placeholder="Enter announcement details..." required>{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="category" class="form-label fw-bold">Category</label>
                        <input type="text" id="category" name="category" class="form-control @error('category') is-invalid @enderror"
                            placeholder="e.g., academic, event, general" value="{{ old('category') }}">
                        <small class="form-text text-muted">Leave blank for general announcements</small>
                        @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="priority" class="form-label fw-bold">Priority <span class="text-danger">*</span></label>
                        <select id="priority" name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                            <option value="">Select priority</option>
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="normal" {{ old('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                        </select>
                        @error('priority')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="">Select status</option>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="save" style="width: 14px;" class="me-1"></i>Create Announcement
                </button>
                <a href="{{ route('announcements.index') }}" class="btn btn-secondary">
                    <i data-lucide="x" style="width: 14px;" class="me-1"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection