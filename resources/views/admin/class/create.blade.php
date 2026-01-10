@extends('layouts.app')

@section('title', 'Add New Class')

@section('content')
    <div class="card mb-4 p-4">
        <h6 class="d-flex align-items-center mb-0">
            <span class="text-muted">All Classes</span>
            <i data-lucide="chevron-right" class="mx-2" style="width: 16px;"></i>
            <span>Add Class</span>
        </h6>
    </div>

    <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-4">
                        <h6 class="card-title">Create New Class</h6>
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.classes.index') }}">
                            <i data-lucide="arrow-left" class="pr-2"></i> Back to List
                        </a>
                    </div>

                    <form action="{{ route('admin.classes.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Class Name</label>
                            <input type="text" name="name" id="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   placeholder="e.g. Primary 1, Grade 10, JSS 1" 
                                   value="{{ old('name') }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" rows="4" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="Briefly describe the class...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">
                                <i data-lucide="save" class="pr-2"></i> Save Class
                            </button>
                            <button type="reset" class="btn btn-light">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Quick Tips</h6>
                    <p class="text-muted small">
                        Ensure class names are unique to avoid confusion in the academic records. 
                        Descriptions can include the room number or teacher's name.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection