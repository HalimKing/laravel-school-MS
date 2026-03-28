<?php

use App\Http\Controllers\AcademicPeriodController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TeacherPasswordController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AssignSujectsController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\CollectFeeController;
use App\Http\Controllers\DataSyncController;
use App\Http\Controllers\EnrollmentListController;
use App\Http\Controllers\EnrollStudentstController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FeeCategoryController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\FinanceReportController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ResultsController;
use App\Http\Controllers\ResultsViewerController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentReportController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login'); // Laravel's default auth redirect
    Route::post('login', [AuthController::class, 'login'])->name('auth.login.post');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');

    // Notifications routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/api/notifications', [NotificationController::class, 'getNotifications'])->name('notifications.get');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    Route::get('students/reports', [StudentController::class, 'report'])->name('students.reports');

    Route::resource('events', EventController::class);
    Route::resource('announcements', AnnouncementController::class);

    // Settings with permission check
    Route::middleware('can:setting.read')->group(function () {
        Route::get('sttings/school', [SettingController::class, 'school'])->name('settings.school');
        Route::get('sttings/system', [SettingController::class, 'system'])->name('settings.system');
    });

    // Reports routes with permission check
    Route::middleware('can:report.read')->prefix('reports')->name('reports.')->group(function () {
        Route::get('students', [StudentReportController::class, 'report'])->name('students');
        Route::get('attendance', [AttendanceReportController::class, 'attendanceReport'])->name('attendance');
        Route::get('finance', [FinanceReportController::class, 'financeReport'])->name('finance');
    });

    // Admin routes with permission protection
    Route::prefix('admin')->name('admin.')->middleware('role:super-admin|admin|teacher|accountant')->group(function () {

        // User Management - require user permissions
        Route::middleware('can:user.read')->resource('users', UserController::class, ['only' => ['index', 'show']]);
        Route::middleware('can:user.create')->get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::middleware('can:user.create')->post('users', [UserController::class, 'store'])->name('users.store');
        Route::middleware('can:user.update')->get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::middleware('can:user.update')->put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::middleware('can:user.delete')->delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::middleware('can:user.update')->post('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');

        // Student Management - require admin permissions
        // Route::middleware('can:academic.create|academic.update')->resource('students', StudentController::class);
        Route::resource('students', StudentController::class)->middleware([
            'index'   => 'can:academic.read',
            'show'    => 'can:academic.read',
            'create'  => 'can:academic.create',
            'store'   => 'can:academic.create',
            'update'  => 'can:academic.update',
            'destroy' => 'can:academic.update',
        ]);
        Route::middleware('can:academic.read')->resource('/classes', ClassController::class);

        // Teacher Management - require admin permissions
        Route::prefix('/teacher/password')->name('teacher.password.')->middleware([
            'index' => 'can:academic.read',
            'search' => 'can:academic.read',
            'update' => 'can:academic.update',
        ])->group(function () {
            Route::get('/', [TeacherPasswordController::class, 'index'])->name('index');
            Route::get('/search', [TeacherPasswordController::class, 'search'])->name('search');
            Route::put('/{id}', [TeacherPasswordController::class, 'update'])->name('update');
        });
        Route::resource('teachers', TeacherController::class)->middleware([
            'index'   => 'can:academic.read',
            'show'    => 'can:academic.read',
            'create'  => 'can:academic.create',
            'store'   => 'can:academic.create',
            'edit'    => 'can:academic.update',
            'update'  => 'can:academic.update',
            'destroy' => 'can:academic.update',
        ]);
        Route::resource('class', ClassController::class)->middleware([
            'index'   => 'can:academic.read',
            'show'    => 'can:academic.read',
            'create'  => 'can:academic.create',
            'store'   => 'can:academic.create',
            'edit'    => 'can:academic.update',
            'update'  => 'can:academic.update',
            'destroy' => 'can:academic.delete',
        ]);

        // Academic & Sessions - require academic permissions
        Route::middleware('can:academic.update')->put('sessions/activate/{id}', [AcademicYearController::class, 'activateSessions'])
            ->name('sessions.activate');
        Route::middleware('can:academic.read')->resource('sessions', AcademicYearController::class);

        // Academics
        Route::prefix('academics')->name('academics.')->middleware(['can:academic.read'])->group(function () {
            Route::resource('academic-periods', AcademicPeriodController::class)->middleware([
                'index'   => 'can:academic.read',
                'show'    => 'can:academic.read',
                'create'  => 'can:academic.create',
                'store'   => 'can:academic.create',
                'edit'    => 'can:academic.update',
                'update'  => 'can:academic.update',
                'destroy' => 'can:academic.delete',
            ]);
            Route::resource('subjects', SubjectController::class)->middleware([
                'index'   => 'can:academic.read',
                'show'    => 'can:academic.read',
                'create'  => 'can:academic.create',
                'store'   => 'can:academic.create',
                'edit'    => 'can:academic.update',
                'update'  => 'can:academic.update',
                'destroy' => 'can:academic.delete',
            ]);

            // Assign subjects - academic create/update
            Route::post('assign-subjects/store', [AssignSujectsController::class, 'store'])->middleware('can:academic.create')->name('assign-subjects.store');
            Route::get('assign-subjects/create', [AssignSujectsController::class, 'create'])->middleware('can:academic.create')->name('assign-subjects.create');
            Route::get('assign-subjects/edit/{id}', [AssignSujectsController::class, 'edit'])->middleware('can:academic.update')->name('assign-subjects.edit');
            Route::put('assign-subjects/update/{id}', [AssignSujectsController::class, 'update'])->middleware('can:academic.update')->name('assign-subjects.update');
            Route::get('assign-subjects', [AssignSujectsController::class, 'index'])->name('assign-subjects.index');
            Route::delete('assign-subjects/{id}', [AssignSujectsController::class, 'destroy'])->middleware('can:academic.delete')->name('assign-subjects.destroy');
        });

        // Attendance - teacher/admin can create, all authenticated can read
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::middleware('can:attendance.create')->get('take-attendance', [AttendanceController::class, 'create'])->name('create');
            Route::middleware('can:attendance.create')->post('take-attendance', [AttendanceController::class, 'store'])->name('store');
            Route::middleware('can:attendance.read')->get('get-students', [AttendanceController::class, 'getStudents'])->name('get-students');
            Route::middleware('can:attendance.read')->get('class-report', [AttendanceController::class, 'classReport'])->name('class-report');
            Route::middleware('can:attendance.read')->get('subject-report', [AttendanceController::class, 'subjectReport'])->name('subject-report');
            Route::middleware('can:attendance.read')->get('student-report', [AttendanceController::class, 'studentReport'])->name('student-report');
            Route::middleware('can:attendance.analytics')->get('analytics', [AttendanceController::class, 'analytics'])->name('analytics');
        });

        // Roles and Permissions Management - admin/super-admin only
        Route::prefix('access-control')->name('access-control.')->group(function () {
            Route::resource('roles', RoleController::class)->middleware([
                'index'   => 'can:role.read',
                'show'    => 'can:role.read',
                'create'  => 'can:role.create',
                'store'   => 'can:role.create',
                'edit'    => 'can:role.update',
                'update'  => 'can:role.update',
                'destroy' => 'can:role.delete',
            ]);
            Route::post('roles/{role}/assign-permissions', [RoleController::class, 'assignPermissions'])->middleware('can:role.update')->name('roles.assign-permissions');

            Route::resource('permissions', PermissionController::class)->middleware([
                'index'   => 'can:permission.read',
                'show'    => 'can:permission.read',
                'create'  => 'can:permission.create',
                'store'   => 'can:permission.create',
                'edit'    => 'can:permission.update',
                'update'  => 'can:permission.update',
                'destroy' => 'can:permission.delete',
            ]);
            Route::post('permissions/bulk-create', [PermissionController::class, 'bulkCreate'])->middleware('can:permission.create')->name('permissions.bulk-create');
        });

        // User Role Management - admin only
        Route::middleware('can:user.update')->prefix('management')->name('management.')->group(function () {
            Route::get('users/roles', [UserRoleController::class, 'index'])->name('users.roles');
            Route::get('users/{user}/assign-roles', [UserRoleController::class, 'edit'])->name('users.assign-roles');
            Route::put('users/{user}/assign-roles', [UserRoleController::class, 'update'])->name('users.update-roles');
            Route::post('users/{user}/attach-role', [UserRoleController::class, 'attach'])->name('users.attach-role');
            Route::post('users/{user}/detach-role', [UserRoleController::class, 'detach'])->name('users.detach-role');
        });

        // Enrollments - academic management
        Route::prefix('enrollments')->name('enrollments.')->middleware('can:academic.create')->group(function () {
            Route::get('enrollment-list', [EnrollmentListController::class, 'index'])
                ->name('enrollment-list.index');
            Route::get('enrollment-list/export', [EnrollmentListController::class, 'export'])
                ->name('enrollment-list.export');

            Route::get('enroll-students', [EnrollStudentstController::class, 'index'])
                ->name('enroll-students.index');
            Route::get('enroll-students/filter', [EnrollStudentstController::class, 'enrollFilter'])
                ->name('enroll-students.filter');
            Route::post('enroll-students/process', [EnrollStudentstController::class, 'enrollProcess'])
                ->name('enroll-students.process');
        });

        // Fee Management - accountant/admin only
        Route::prefix('fee-management')->name('fee-management.')->middleware('can:fee.read')->group(function () {
            Route::resource('fee-categories', FeeCategoryController::class)->middleware([
                'index'   => 'can:fee.read',
                'show'    => 'can:fee.read',
                'create'  => 'can:fee.create',
                'store'   => 'can:fee.create',
                'edit'    => 'can:fee.update',
                'update'  => 'can:fee.update',
                'destroy' => 'can:fee.delete',
            ]);
            Route::resource('fees', FeeController::class)->middleware([
                'index'   => 'can:fee.read',
                'show'    => 'can:fee.read',
                'create'  => 'can:fee.create',
                'store'   => 'can:fee.create',
                'edit'    => 'can:fee.update',
                'update'  => 'can:fee.update',
                'destroy' => 'can:fee.delete',
            ]);
            Route::get('collect-fees', [CollectFeeController::class, 'index'])->middleware('can:fee.collect')->name('collect-fees.index');
            Route::post('collect-fees', [CollectFeeController::class, 'store'])->middleware('can:fee.collect')->name('collect-fees.store');
            Route::get('collect-fees/print-receipt/{id}', [CollectFeeController::class, 'printReceipt'])->middleware('can:fee.collect')->name('collect-fees.receipt');
        });

        // Results Management - academic management
        Route::prefix('results-management')->name('results-management.')->middleware('can:academic.create')->group(function () {
            Route::resource('assessments', AssessmentController::class)->middleware([
                'index'   => 'can:academic.read',
                'show'    => 'can:academic.read',
                'create'  => 'can:academic.create',
                'store'   => 'can:academic.create',
                'edit'    => 'can:academic.update',
                'update'  => 'can:academic.update',
                'destroy' => 'can:academic.delete',
            ]);
        });

        // Data Synchronization - admin only
        Route::prefix('data-sync')->name('data-sync.')->middleware('can:user.read')->group(function () {
            Route::get('/', [DataSyncController::class, 'index'])->name('index');
            Route::post('teachers', [DataSyncController::class, 'syncTeachers'])->name('sync-teachers');
            Route::post('guardians', [DataSyncController::class, 'syncGuardians'])->name('sync-guardians');
            Route::post('students', [DataSyncController::class, 'syncStudents'])->name('sync-students');
            Route::post('all', [DataSyncController::class, 'syncAll'])->name('sync-all');
            Route::post('check-duplicates', [DataSyncController::class, 'checkDuplicates'])->name('check-duplicates');
        });
    });

    // Results Viewer - Role-based view for parents, students, admins - OUTSIDE admin prefix
    Route::prefix('results-viewer')->name('results-viewer.')->middleware('auth')->group(function () {
        Route::get('/', [ResultsViewerController::class, 'viewResults'])->name('view');
        Route::get('/students', [ResultsViewerController::class, 'getStudents'])->name('get-students'); // AJAX
        Route::post('/export', [ResultsViewerController::class, 'exportResults'])->name('export');
    });

    // Results - teacher and admin can manage, students/parents can read
    Route::prefix('results')->name('results.')->middleware('can:academic.read')->group(function () {
        Route::get('/', [ResultsController::class, 'index'])->name('index');
        Route::get('/export', [ResultsController::class, 'export'])->name('export');
        Route::delete('/delete-student-results', [ResultsController::class, 'deleteStudentResults'])->middleware('can:academic.delete')->name('delete-student-results');
        Route::delete('/{id}', [ResultsController::class, 'destroy'])->middleware('can:academic.delete')->name('destroy');
        Route::get('/single-upload', [ResultsController::class, 'singleUpload'])->middleware('can:academic.create')->name('single-upload');
        Route::post('/single-upload', [ResultsController::class, 'storeSingleResult'])->middleware('can:academic.create')->name('single-upload.store');
        Route::post('/check-duplicates', [ResultsController::class, 'checkDuplicates'])->middleware('can:academic.create')->name('check-duplicates');
        Route::get('/get-students/{academicYearId}/{classId}', [ResultsController::class, 'getStudentsByClass'])->name('get-students');
        Route::get('/get-assessments/{subjectId}/{classId}', [ResultsController::class, 'getAssessmentsBySubject'])->name('get-assessments');
        Route::get('/bulk-upload', [ResultsController::class, 'bulkUpload'])->middleware('can:academic.create')->name('bulk-upload');
        Route::post('/bulk-upload', [ResultsController::class, 'processBulkUpload'])->middleware('can:academic.create')->name('bulk-upload.store');
        Route::get('/download-template/{subjectId}/{classId}', [ResultsController::class, 'downloadTemplate'])->middleware('can:academic.create')->name('download-template');
    });
});
