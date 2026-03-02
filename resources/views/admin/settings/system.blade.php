@extends('layouts.app')

@section('title', 'System Settings')

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">System Settings</h6>
    </div>
</div>

@include('includes.message')

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="#">
                    @csrf

                    <h6 class="fw-bold mb-3">General Settings</h6>

                    <div class="mb-3">
                        <label for="app_name" class="form-label fw-bold">Application Name</label>
                        <input type="text" class="form-control" id="app_name" name="app_name"
                            value="{{ setting('app_name', 'School Management System') }}" placeholder="Enter app name">
                    </div>

                    <div class="mb-3">
                        <label for="timezone" class="form-label fw-bold">Timezone</label>
                        <select class="form-control" id="timezone" name="timezone">
                            <option value="UTC" {{ setting('timezone', 'UTC') === 'UTC' ? 'selected' : '' }}>UTC</option>
                            <option value="Africa/Lagos" {{ setting('timezone') === 'Africa/Lagos' ? 'selected' : '' }}>Africa/Lagos (WAT)</option>
                            <option value="Africa/Johannesburg" {{ setting('timezone') === 'Africa/Johannesburg' ? 'selected' : '' }}>Africa/Johannesburg (SAST)</option>
                            <option value="Europe/London" {{ setting('timezone') === 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT)</option>
                            <option value="America/New_York" {{ setting('timezone') === 'America/New_York' ? 'selected' : '' }}>America/New York (EST)</option>
                            <option value="Asia/Dubai" {{ setting('timezone') === 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai (GST)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="currency" class="form-label fw-bold">Currency Symbol</label>
                        <input type="text" class="form-control" id="currency" name="currency"
                            value="{{ setting('currency', '₦') }}" placeholder="Enter currency symbol" maxlength="3">
                    </div>

                    <div class="mb-3">
                        <label for="date_format" class="form-label fw-bold">Date Format</label>
                        <select class="form-control" id="date_format" name="date_format">
                            <option value="Y-m-d" {{ setting('date_format', 'Y-m-d') === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                            <option value="d-m-Y" {{ setting('date_format') === 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY</option>
                            <option value="m/d/Y" {{ setting('date_format') === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                            <option value="d/m/Y" {{ setting('date_format') === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                        </select>
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold mb-3">Academic Settings</h6>

                    <div class="mb-3">
                        <label for="academic_year_start" class="form-label fw-bold">Academic Year Start Month</label>
                        <select class="form-control" id="academic_year_start" name="academic_year_start">
                            <option value="1" {{ setting('academic_year_start', 1) == 1 ? 'selected' : '' }}>January</option>
                            <option value="9" {{ setting('academic_year_start') == 9 ? 'selected' : '' }}>September</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="attendance_threshold" class="form-label fw-bold">Attendance Threshold (%) for Exam Eligibility</label>
                        <input type="number" class="form-control" id="attendance_threshold" name="attendance_threshold"
                            value="{{ setting('attendance_threshold', 75) }}" min="0" max="100">
                        <small class="text-muted">Students with attendance below this percentage may not be eligible to sit exams</small>
                    </div>

                    <div class="mb-3">
                        <label for="late_mark_after_minutes" class="form-label fw-bold">Mark as Late After (minutes)</label>
                        <input type="number" class="form-control" id="late_mark_after_minutes" name="late_mark_after_minutes"
                            value="{{ setting('late_mark_after_minutes', 15) }}" min="1">
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold mb-3">Notification Settings</h6>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="email_notifications" name="email_notifications"
                                {{ setting('email_notifications', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_notifications">
                                Enable Email Notifications
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="sms_notifications" name="sms_notifications"
                                {{ setting('sms_notifications', false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="sms_notifications">
                                Enable SMS Notifications
                            </label>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold mb-3">Maintenance</h6>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="maintenance_mode" name="maintenance_mode"
                                {{ setting('maintenance_mode', false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="maintenance_mode">
                                Enable Maintenance Mode
                            </label>
                        </div>
                        <small class="text-muted">When enabled, only administrators can access the system</small>
                    </div>

                    <div class="mb-3">
                        <label for="maintenance_message" class="form-label fw-bold">Maintenance Message</label>
                        <textarea class="form-control" id="maintenance_message" name="maintenance_message" rows="3"
                            placeholder="Message to display during maintenance">{{ setting('maintenance_message', 'System is under maintenance. Please check back later.') }}</textarea>
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
                <h6 class="card-title fw-bold mb-0">System Information</h6>
            </div>
            <div class="card-body">
                <p class="text-sm mb-2">
                    <strong>Laravel Version:</strong><br>
                    <code class="text-muted">{{ app()->version() }}</code>
                </p>
                <p class="text-sm mb-2">
                    <strong>PHP Version:</strong><br>
                    <code class="text-muted">{{ phpversion() }}</code>
                </p>
                <p class="text-sm mb-2">
                    <strong>Environment:</strong><br>
                    <span class="badge bg-info">{{ config('app.env') }}</span>
                </p>
                <hr>
                <p class="text-sm"><strong>Configure:</strong></p>
                <ul class="text-sm text-muted">
                    <li>Application name</li>
                    <li>Timezone & date format</li>
                    <li>Academic settings</li>
                    <li>Notification preferences</li>
                    <li>Maintenance mode</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection