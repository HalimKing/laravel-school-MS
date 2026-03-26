@extends('layouts.app')

@section('content')
<div class="page-content">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Data Synchronization</h3>
    </div>

    <!-- Success Messages -->
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        @if (session('sync_result'))
        <div class="mt-3">
            <strong>{{ session('sync_result')['type'] }} Sync Results:</strong>
            <ul class="mb-0 mt-2">
                <li>Created: {{ session('sync_result')['synced'] }}</li>
                <li>Updated: {{ session('sync_result')['updated'] }}</li>
                <li>Skipped: {{ session('sync_result')['skipped'] }}</li>
                <li>Total Processed: {{ session('sync_result')['total'] }}</li>
            </ul>
        </div>
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Info Alert -->
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <strong>📋 About Data Sync:</strong>
        <p class="mb-2">This feature allows you to synchronize user data from different tables to the main users table for authentication.</p>
        <ul class="mb-0">
            <li><strong>Teachers:</strong> Syncs teachers from the teachers table with their email and password</li>
            <li><strong>Guardians:</strong> Syncs parent/guardian data from students table using parent email</li>
            <li><strong>Students (Optional):</strong> Creates user accounts for students using their student ID</li>
            <li><strong>All:</strong> Performs all synchronizations at once</li>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <!-- Sync Cards -->
    <div class="row">
        <!-- Teachers Sync -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i data-lucide="users" class="me-2"></i>Sync Teachers
                    </h5>
                    <p class="card-text text-muted small mb-3">
                        Synchronize all teachers from the teachers table to the users table. Each teacher will be able to login with their email.
                    </p>
                    <form method="POST" action="{{ route('admin.data-sync.sync-teachers') }}" class="d-inline">
                        @csrf
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="overwrite" id="overwrite-teachers">
                            <label class="form-check-label" for="overwrite-teachers">
                                Overwrite existing users
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Sync Teachers</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Guardians Sync -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i data-lucide="shield-alert" class="me-2"></i>Sync Guardians/Parents
                    </h5>
                    <p class="card-text text-muted small mb-3">
                        Synchronize parent/guardian information from the students table. Creates accounts for guardians to access student information.
                    </p>
                    <form method="POST" action="{{ route('admin.data-sync.sync-guardians') }}" class="d-inline">
                        @csrf
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="overwrite" id="overwrite-guardians">
                            <label class="form-check-label" for="overwrite-guardians">
                                Overwrite existing users
                            </label>
                        </div>
                        <button type="submit" class="btn btn-warning btn-sm">Sync Guardians</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Students Sync -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i data-lucide="book" class="me-2"></i>Sync Students
                    </h5>
                    <p class="card-text text-muted small mb-3">
                        Creates user accounts for all students. Student login will be: student_id@schoolms.com
                    </p>
                    <form method="POST" action="{{ route('admin.data-sync.sync-students') }}" class="d-inline">
                        @csrf
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="overwrite" id="overwrite-students">
                            <label class="form-check-label" for="overwrite-students">
                                Overwrite existing users
                            </label>
                        </div>
                        <button type="submit" class="btn btn-success btn-sm">Sync Students</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sync All -->
        <div class="col-md-6 mb-4">
            <div class="card border-primary">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i data-lucide="zap" class="me-2"></i>Sync All Data
                    </h5>
                    <p class="card-text text-muted small mb-3">
                        Performs all synchronizations at once: Teachers, Guardians, and Students.
                    </p>
                    <form method="POST" action="{{ route('admin.data-sync.sync-all') }}" class="d-inline">
                        @csrf
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="overwrite" id="overwrite-all">
                            <label class="form-check-label" for="overwrite-all">
                                Overwrite existing users
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg" onclick="return confirm('This will sync all data. Continue?')">
                            Sync All
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Options -->
    <div class="card mt-4">
        <div class="card-header">
            <h6 class="card-title mb-0">Additional Options</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-3">Check for Duplicates</h6>
                    <p class="text-muted small mb-3">
                        Check for duplicate emails in the system before syncing to avoid conflicts.
                    </p>
                    <form method="POST" action="{{ route('admin.data-sync.check-duplicates') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                            <i data-lucide="search" class="me-2"></i>Check Duplicates
                        </button>
                    </form>
                </div>
            </div>

            @if (session('duplicates'))
            <div class="mt-4">
                <h6>Duplicate Emails Found:</h6>
                <div class="alert alert-warning">
                    Found {{ session('duplicates')['duplicate_teacher_emails'] }} duplicate teacher email(s)
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Command Line Usage -->
    <div class="card mt-4">
        <div class="card-header">
            <h6 class="card-title mb-0">Command Line Usage</h6>
        </div>
        <div class="card-body">
            <p class="text-muted small mb-3">You can also run synchronization from the command line:</p>
            <div class="bg-dark text-light p-3 rounded">
                <code>
                    # Sync all data<br>
                    php artisan sync:data all<br>
                    <br>
                    # Sync specific source<br>
                    php artisan sync:data teachers<br>
                    php artisan sync:data guardians<br>
                    php artisan sync:data students<br>
                    <br>
                    # Overwrite existing users<br>
                    php artisan sync:data all --overwrite<br>
                    <br>
                    # Check for duplicates first<br>
                    php artisan sync:data all --check-duplicates<br>
                </code>
            </div>
        </div>
    </div>

</div>

<script src="{{ asset('assets/js/feather.min.js') }}"></script>
<script>
    feather.replace();
</script>
@endsection