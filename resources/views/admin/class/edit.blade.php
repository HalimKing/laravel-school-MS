@extends('layouts.app')

@section('title', 'Edit Class')

@section('content')
    <div class="card mb-4 p-4">
        <h6 class="d-flex align-items-center mb-0">
            <span class="text-muted">All Classes</span>
            <i data-lucide="chevron-right" class="mx-2" style="width: 16px;"></i>
            <span>Edit Class</span>
            <i data-lucide="chevron-right" class="mx-2" style="width: 16px;"></i>
            <span class="text-primary">{{ $class->name }}</span>
        </h6>
    </div>

    <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-4">
                        <h6 class="card-title text-uppercase">Modify Class Details</h6>
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.classes.index') }}">
                            <i data-lucide="arrow-left" class="pr-2"></i> Back to List
                        </a>
                    </div>

                    <form action="{{ route('admin.classes.update', $class->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Class Name</label>
                            <input type="text" name="name" id="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $class->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" rows="4" 
                                      class="form-control @error('description') is-invalid @enderror">{{ old('description', $class->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-4 border-top pt-3">
                            <button type="submit" class="btn btn-success me-2">
                                <i data-lucide="check-circle" class="pr-2"></i> Update Class
                            </button>
                            <a href="{{ route('admin.classes.index') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0">
                <div class="card-body">
                    <h6 class="card-title d-flex align-items-center">
                        <i data-lucide="info" class="me-2 text-info"></i> Information
                    </h6>
                    <p class="text-muted small">
                        You are currently editing <strong>{{ $class->name }}</strong>. 
                        Changes will be reflected across the system immediately after saving, including in student enrollments and schedules.
                    </p>
                    <hr>
                    <p class="text-muted small">
                        <strong>Created at:</strong> {{ $class->created_at->format('M d, Y') }} <br>
                        <strong>Last Updated:</strong> {{ $class->updated_at->diffForHumans() }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection