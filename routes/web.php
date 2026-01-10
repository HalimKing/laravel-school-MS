<?php

use App\Http\Controllers\AcademicPeriodController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\TeacherPasswordController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\EnrollmentListController;
use App\Http\Controllers\EnrollStudentstController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FeeCategoryController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\FinanceReportController;
use App\Http\Controllers\ResultsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentReportController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->name('dashboard');

Route::get('students/reports', [StudentController::class, 'report'])->name('students.reports');




Route::resource('subjects', SubjectController::class);
Route::resource('events', EventController::class);
Route::resource('users', UserController::class);
Route::resource('roles', RolesController::class);
Route::resource('announcements', AnnouncementController::class);

Route::get('sttings/school', [SettingController::class, 'school'])->name('settings.school');
Route::get('sttings/system', [SettingController::class, 'system'])->name('settings.system');

// reports routes
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('students', [StudentReportController::class, 'report'])->name('students');
    Route::get('attendance', [AttendanceReportController::class, 'attendanceReport'])->name('attendance');
    Route::get('finance', [FinanceReportController::class, 'financeReport'])->name('finance');
});

Route::prefix('admin')->name('admin.')->group(function () {

    

    Route::resource('/students', StudentController::class);
    Route::resource('/classes', ClassController::class);
    
    Route::prefix('/teacher/password')->name('teacher.password.')->group(function () {
    // Display the search/reset page
        Route::get('/', [TeacherPasswordController::class, 'index'])->name('index');
        
        // Process the search query
        Route::get('/search', [TeacherPasswordController::class, 'search'])->name('search');
        
        // Perform the password update
        Route::put('/{id}', [TeacherPasswordController::class, 'update'])->name('update');
    });
    Route::resource('teachers', TeacherController::class);
    Route::resource('class', ClassController::class);
    Route::put('sessions/activate/{id}', [AcademicYearController::class, 'activateSessions'])
    ->name('sessions.activate');
    Route::resource('sessions', AcademicYearController::class);


    Route::prefix('academics')->name('academics.')->group(function () {
        Route::resource('academic-periods', AcademicPeriodController::class);
    });

    Route::prefix('enrollments')->name('enrollments.')->group(function () {
        Route::get('enrollment-list', [EnrollmentListController::class, 'index'])
        ->name('enrollment-list.index');
        Route::get('enrollment-list/export', [EnrollmentListController::class, 'export'])
        ->name('enrollment-list.export');

        Route::get('enroll-students', [EnrollStudentstController::class, 'index'])
        ->name('enroll-students.index');
        Route::get('enroll-students/filter', [EnrollStudentstController::class, 'enrollFilter'])
        ->name('enroll-students.filter');
        // enrollment process
        Route::post('enroll-students/process', [EnrollStudentstController::class, 'enrollProcess'])
        ->name('enroll-students.process');

        // filter en


    });

    // fees routes
    Route::prefix('fee-management')->name('fee-management.')->group(function () {
        Route::resource('fee-categories', FeeCategoryController::class);
        Route::resource('fees', FeeController::class);
        
    });
  

});

Route::prefix('results')->name('results.')->group(function () {
    Route::get('/single-upload', [ResultsController::class, 'singleUpload'])->name('single-upload');
    Route::get('/bulk-upload', [ResultsController::class, 'bulkUpload'])->name('bulk-upload');
});


