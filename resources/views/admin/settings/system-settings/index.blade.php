@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-1">System Settings</h4>
                <p class="text-muted mb-0">Manage global application configurations and branding.</p>
            </div>
            <button type="submit" form="settingsForm" class="btn btn-primary px-4 shadow-sm rounded-pill">
                <i data-lucide="save" class="me-2" style="width: 18px;"></i>
                Save Changes
            </button>
        </div>
    </div>

    @include('includes.message')

    <form id="settingsForm" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Sidebar Navigation -->
            <div class="col-lg-3 mb-4">
                <div class="card border-0 shadow-sm p-2">
                    <div class="nav flex-column nav-pills" id="settings-tabs" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active d-flex align-items-center mb-1 py-3" id="general-tab" data-bs-toggle="pill" data-bs-target="#general" type="button" role="tab">
                            <i data-lucide="settings" class="me-3" style="width: 20px;"></i>
                            General Info
                        </button>
                        <button class="nav-link d-flex align-items-center mb-1 py-3" id="branding-tab" data-bs-toggle="pill" data-bs-target="#branding" type="button" role="tab">
                            <i data-lucide="image" class="me-3" style="width: 20px;"></i>
                            Branding
                        </button>
                        <button class="nav-link d-flex align-items-center mb-1 py-3" id="localization-tab" data-bs-toggle="pill" data-bs-target="#localization" type="button" role="tab">
                            <i data-lucide="globe" class="me-3" style="width: 20px;"></i>
                            Localization
                        </button>
                        <button class="nav-link d-flex align-items-center py-3 text-danger" id="system-tab" data-bs-toggle="pill" data-bs-target="#system" type="button" role="tab">
                            <i data-lucide="shield-alert" class="me-3" style="width: 20px;"></i>
                            System & Security
                        </button>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="col-lg-9">
                <div class="tab-content border-0" id="settings-tabsContent">
                    
                    <!-- General Settings -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h5 class="card-title fw-bold mb-4">General Configuration</h5>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Application Name</label>
                                        <input type="text" name="app_name" class="form-control" value="{{ old('app_name', config('app.name')) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Contact Email</label>
                                        <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', 'admin@school.edu') }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Organization Address</label>
                                        <textarea name="address" class="form-control" rows="3">{{ old('address') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Branding Settings -->
                    <div class="tab-pane fade" id="branding" role="tabpanel">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h5 class="card-title fw-bold mb-4">Logo & Favicon</h5>
                                <div class="row g-4">
                                    <div class="col-md-6 text-center border-end">
                                        <div class="mb-3">
                                            <p class="small fw-bold text-muted text-uppercase">System Logo</p>
                                            <div class="bg-light rounded p-4 mb-3 d-inline-block">
                                                <img src="{{ asset('storage/logo.png') }}" id="logoPreview" alt="Logo" style="max-height: 80px;">
                                            </div>
                                            <input type="file" name="logo" class="form-control form-control-sm" onchange="previewImage(this, 'logoPreview')">
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-center">
                                        <div class="mb-3">
                                            <p class="small fw-bold text-muted text-uppercase">Favicon</p>
                                            <div class="bg-light rounded p-4 mb-3 d-inline-block">
                                                <img src="{{ asset('favicon.ico') }}" id="faviconPreview" alt="Favicon" style="width: 32px; height: 32px;">
                                            </div>
                                            <input type="file" name="favicon" class="form-control form-control-sm" onchange="previewImage(this, 'faviconPreview')">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Localization Settings -->
                    <div class="tab-pane fade" id="localization" role="tabpanel">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h5 class="card-title fw-bold mb-4">Date & Time</h5>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Timezone</label>
                                        <select name="timezone" class="form-select">
                                            <option value="UTC">UTC</option>
                                            <option value="Asia/Kolkata" selected>Asia/Kolkata</option>
                                            <option value="America/New_York">America/New_York</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Date Format</label>
                                        <select name="date_format" class="form-select">
                                            <option value="d/m/Y">DD/MM/YYYY</option>
                                            <option value="Y-m-d">YYYY-MM-DD</option>
                                            <option value="M d, Y">Oct 12, 2023</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Settings -->
                    <div class="tab-pane fade" id="system" role="tabpanel">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h5 class="card-title fw-bold mb-4">Security & Access</h5>
                                
                                <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                                    <div>
                                        <h6 class="mb-0 fw-bold">Maintenance Mode</h6>
                                        <p class="small text-muted mb-0">Only administrators can access the system when enabled.</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="maintenance_mode" style="width: 2.5em; height: 1.25em;">
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                                    <div>
                                        <h6 class="mb-0 fw-bold">User Registration</h6>
                                        <p class="small text-muted mb-0">Allow new students/staff to register accounts.</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="allow_registration" checked style="width: 2.5em; height: 1.25em;">
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="mb-0 fw-bold text-danger">Debug Mode</h6>
                                        <p class="small text-muted mb-0">Show detailed error messages. Disable in production.</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="debug_mode" style="width: 2.5em; height: 1.25em;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .nav-pills .nav-link {
        color: #64748b;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.2s;
    }
    
    .nav-pills .nav-link.active {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
    }

    .nav-pills .nav-link:hover:not(.active) {
        background-color: #f8f9fa;
    }

    .form-switch .form-check-input {
        cursor: pointer;
    }

    .form-control:focus, .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
    }
</style>

<script>
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Refresh icons for dynamic tabs
    document.querySelectorAll('button[data-bs-toggle="pill"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', () => {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    });
</script>
@endsection