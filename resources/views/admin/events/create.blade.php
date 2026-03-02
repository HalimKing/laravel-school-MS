@extends('layouts.app')

@section('title', 'Create Event')

@section('content')

<div class="card mb-4 p-4">
    <h6 class="mb-0 text-uppercase fw-bold">Create New Event</h6>
</div>

@include('includes.message')

<div class="card">
    <div class="card-body">
        <form action="{{ route('events.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold">Event Title <span class="text-danger">*</span></label>
                        <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
                            placeholder="Enter event title" value="{{ old('title') }}" required>
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                        <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                            rows="6" placeholder="Enter event details..." required>{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="event_date" class="form-label fw-bold">Event Date <span class="text-danger">*</span></label>
                            <input type="date" id="event_date" name="event_date" class="form-control @error('event_date') is-invalid @enderror"
                                value="{{ old('event_date') }}" required>
                            @error('event_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="event_end_date" class="form-label fw-bold">End Date</label>
                            <input type="date" id="event_end_date" name="event_end_date" class="form-control @error('event_end_date') is-invalid @enderror"
                                value="{{ old('event_end_date') }}">
                            @error('event_end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_time" class="form-label fw-bold">Start Time</label>
                            <input type="time" id="start_time" name="start_time" class="form-control @error('start_time') is-invalid @enderror"
                                value="{{ old('start_time') }}">
                            @error('start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="end_time" class="form-label fw-bold">End Time</label>
                            <input type="time" id="end_time" name="end_time" class="form-control @error('end_time') is-invalid @enderror"
                                value="{{ old('end_time') }}">
                            @error('end_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label fw-bold">Additional Notes</label>
                        <textarea id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror"
                            rows="3" placeholder="Any additional information...">{{ old('notes') }}</textarea>
                        @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="location" class="form-label fw-bold">Location</label>
                        <input type="text" id="location" name="location" class="form-control @error('location') is-invalid @enderror"
                            placeholder="e.g., School Hall, Sports Field" value="{{ old('location') }}">
                        @error('location')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label fw-bold">Category</label>
                        <input type="text" id="category" name="category" class="form-control @error('category') is-invalid @enderror"
                            placeholder="e.g., sports, academic, cultural" value="{{ old('category') }}">
                        @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="">Select status</option>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="save" style="width: 14px;" class="me-1"></i>Create Event
                </button>
                <a href="{{ route('events.index') }}" class="btn btn-secondary">
                    <i data-lucide="x" style="width: 14px;" class="me-1"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection