@extends('layouts.app')

@section('title', 'School Settings')

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">School Settings</h6>
    </div>
</div>

@include('includes.message')

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('settings.school.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="school_name" class="form-label fw-bold">School Name</label>
                        <input type="text" class="form-control" id="school_name" name="school_name"
                            value="{{ setting('school_name', '') }}" placeholder="Enter school name">
                    </div>

                    <div class="mb-3">
                        <label for="school_code" class="form-label fw-bold">School Code/ID</label>
                        <input type="text" class="form-control" id="school_code" name="school_code"
                            value="{{ setting('school_code', '') }}" placeholder="Enter school code">
                    </div>

                    <div class="mb-3">
                        <label for="school_motto" class="form-label fw-bold">School Motto</label>
                        <input type="text" class="form-control" id="school_motto" name="school_motto"
                            value="{{ setting('school_motto', '') }}" placeholder="Enter school motto">
                    </div>

                    <div class="mb-3">
                        <label for="principal_name" class="form-label fw-bold">Principal Name</label>
                        <input type="text" class="form-control" id="principal_name" name="principal_name"
                            value="{{ setting('principal_name', '') }}" placeholder="Enter principal name">
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label fw-bold">School Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"
                            placeholder="Enter school address">{{ setting('address', '') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label fw-bold">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone"
                            value="{{ setting('phone', '') }}" placeholder="Enter phone number">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="{{ setting('email', '') }}" placeholder="Enter email address">
                    </div>

                    <div class="mb-3">
                        <label for="website" class="form-label fw-bold">Website</label>
                        <input type="url" class="form-control" id="website" name="website"
                            value="{{ setting('website', '') }}" placeholder="Enter website URL">
                    </div>

                    <div class="mb-3">
                        <label for="logo" class="form-label fw-bold">School Logo</label>
                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                        @if(setting('logo'))
                        <div class="mt-2">
                            <img src="{{ setting('logo') }}" alt="School Logo" style="max-width: 150px;">
                        </div>
                        @endif
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save" style="width: 14px;" class="me-1"></i>Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="card-title fw-bold mb-0">Information</h6>
            </div>
            <div class="card-body">
                <p class="text-sm text-muted">
                    Configure basic school information that will be used throughout the system.
                </p>
                <hr>
                <p class="text-sm"><strong>Settings included:</strong></p>
                <ul class="text-sm text-muted">
                    <li>School name and code</li>
                    <li>Motto and branding</li>
                    <li>Principal information</li>
                    <li>Contact details</li>
                    <li>School logo</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection