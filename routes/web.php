<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseCategoryController;
use App\Http\Controllers\CourseWeekController;
use App\Http\Controllers\CourseEnrollmentController;
use App\Http\Controllers\CourseMediaController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrainerProgressController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware(['guest', 'secure.headers'])->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware(['auth', 'active', 'secure.headers'])->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::redirect('/panel/superadmin', '/dashboard')
        ->middleware('role:superadmin')->name('panel.superadmin');
    Route::get('/panel/admin', [PanelController::class, 'show'])
        ->defaults('role', 'admin')->middleware('role:admin')->name('panel.admin');
    Route::get('/panel/manager-hr', [PanelController::class, 'show'])
        ->defaults('role', 'manager_hr')->middleware('role:manager_hr')->name('panel.manager_hr');
    Route::get('/panel/it', [PanelController::class, 'show'])
        ->defaults('role', 'it')->middleware('role:it')->name('panel.it');
    Route::get('/panel/trainer', [PanelController::class, 'show'])
        ->defaults('role', 'trainer')->middleware('role:trainer')->name('panel.trainer');
    Route::get('/panel/student', [PanelController::class, 'show'])
        ->defaults('role', 'student')->middleware('role:student')->name('panel.student');

    Route::middleware('role:superadmin,admin')->group(function (): void {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::put('/users/{managedUser}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{managedUser}', [UserManagementController::class, 'destroy'])->name('users.destroy');

        Route::get('/enrollments', [CourseEnrollmentController::class, 'index'])->name('enrollments.index');
        Route::post('/enrollments', [CourseEnrollmentController::class, 'store'])->name('enrollments.store');
        Route::delete('/enrollments/{enrollment}', [CourseEnrollmentController::class, 'destroy'])->name('enrollments.destroy');

        Route::post('/courses/{course}/weeks', [CourseWeekController::class, 'storeWeek'])->name('courses.weeks.store');
        Route::put('/course-weeks/{week}', [CourseWeekController::class, 'updateWeek'])->name('course-weeks.update');
        Route::delete('/course-weeks/{week}', [CourseWeekController::class, 'destroyWeek'])->name('course-weeks.destroy');
        Route::post('/course-weeks/{week}/sessions', [CourseWeekController::class, 'storeSession'])->name('course-weeks.sessions.store');
        Route::put('/course-sessions/{session}', [CourseWeekController::class, 'updateSession'])->name('course-sessions.update');
        Route::delete('/course-sessions/{session}', [CourseWeekController::class, 'destroySession'])->name('course-sessions.destroy');
        Route::put('/course-session-items/{item}', [CourseWeekController::class, 'updateItem'])->name('course-session-items.update');
    });

    Route::get('/course-categories', [CourseCategoryController::class, 'index'])->name('course-categories.index');
    Route::post('/course-categories', [CourseCategoryController::class, 'store'])->name('course-categories.store');
    Route::put('/course-categories/{category}', [CourseCategoryController::class, 'update'])->name('course-categories.update');
    Route::delete('/course-categories/{category}', [CourseCategoryController::class, 'destroy'])->name('course-categories.destroy');

    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
    Route::get('/course-session-items/{item}/secure-view', [CourseMediaController::class, 'view'])->name('course-session-items.media.view');
    Route::get('/course-session-items/{item}/stream', [CourseMediaController::class, 'stream'])->name('course-session-items.media.stream');

    Route::middleware('role:trainer')->group(function (): void {
        Route::get('/trainer/progress', [TrainerProgressController::class, 'index'])->name('trainer.progress');
    });

    Route::middleware('role:student')->group(function (): void {
        Route::get('/my-courses', [CourseEnrollmentController::class, 'myCourses'])->name('student.courses');
        Route::get('/my-courses/{course}', [CourseEnrollmentController::class, 'showEnrolledCourse'])->name('student.courses.show');
    });
});
