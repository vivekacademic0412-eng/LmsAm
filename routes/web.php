<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BroadcastNotificationController;
use App\Http\Controllers\CourseCategoryController;
use App\Http\Controllers\CourseWeekController;
use App\Http\Controllers\CourseEnrollmentController;
use App\Http\Controllers\CourseItemSubmissionController;
use App\Http\Controllers\CourseMediaController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DemoFeatureVideoController;
use App\Http\Controllers\DemoReviewVideoController;
use App\Http\Controllers\DemoTaskController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrainerCourseItemsController;
use App\Http\Controllers\TrainerProgressController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware(['guest', 'secure.headers'])->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware(['auth', 'active', 'secure.headers', 'activity.log'])->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/notifications/{notification}/read', [DashboardController::class, 'markNotificationRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [DashboardController::class, 'markAllNotificationsRead'])->name('notifications.read-all');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::redirect('/panel/superadmin', '/dashboard')
        ->middleware('role:superadmin')->name('panel.superadmin');
    Route::get('/panel/admin', [PanelController::class, 'show'])
        ->defaults('role', 'admin')->middleware('role:admin')->name('panel.admin');
    Route::redirect('/panel/demo', '/dashboard')
        ->middleware('role:demo')->name('panel.demo');
    Route::get('/panel/manager-hr', [PanelController::class, 'show'])
        ->defaults('role', 'manager_hr')->middleware('role:manager_hr')->name('panel.manager_hr');
    Route::get('/panel/manager-hr/reports/{report}/{format}', [PanelController::class, 'exportManagerHrReport'])
        ->middleware('role:manager_hr')->name('panel.manager_hr.export');
    Route::get('/panel/it', [PanelController::class, 'show'])
        ->defaults('role', 'it')->middleware('role:it')->name('panel.it');
    Route::get('/panel/trainer', [PanelController::class, 'show'])
        ->defaults('role', 'trainer')->middleware('role:trainer')->name('panel.trainer');
    Route::get('/panel/student', [PanelController::class, 'show'])
        ->defaults('role', 'student')->middleware('role:student')->name('panel.student');

    Route::get('/demo-feature-video/{video}', [DemoFeatureVideoController::class, 'show'])
        ->name('demo-feature-video.show');

    Route::get('/demo-tasks/{demoTask}/download', [DemoTaskController::class, 'downloadResource'])
        ->name('demo-tasks.download');
    Route::get('/demo-tasks/{demoTask}/video', [DemoTaskController::class, 'showVideo'])
        ->name('demo-tasks.video');
    Route::put('/course-item-submissions/{submission}/review', [CourseItemSubmissionController::class, 'review'])
        ->name('course-item-submissions.review');

    Route::middleware('role:superadmin,admin')->group(function (): void {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::put('/users/{managedUser}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{managedUser}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{managedUser}/resend-email', [UserManagementController::class, 'resendWelcomeEmail'])->name('users.resend-email');

        Route::get('/enrollments', [CourseEnrollmentController::class, 'index'])->name('enrollments.index');
        Route::post('/enrollments', [CourseEnrollmentController::class, 'store'])->name('enrollments.store');
        Route::put('/enrollments/{enrollment}', [CourseEnrollmentController::class, 'update'])->name('enrollments.update');
        Route::delete('/enrollments/{enrollment}', [CourseEnrollmentController::class, 'destroy'])->name('enrollments.destroy');
        Route::post('/enrollments/{enrollment}/resend-email', [CourseEnrollmentController::class, 'resendAssignmentEmail'])->name('enrollments.resend-email');

        Route::get('/submissions', [CourseItemSubmissionController::class, 'index'])->name('submissions.index');
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

        Route::get('/broadcast-notifications', [BroadcastNotificationController::class, 'index'])->name('broadcast-notifications.index');
        Route::post('/broadcast-notifications', [BroadcastNotificationController::class, 'store'])->name('broadcast-notifications.store');

        Route::post('/courses/{course}/weeks', [CourseWeekController::class, 'storeWeek'])->name('courses.weeks.store');
        Route::put('/course-weeks/{week}', [CourseWeekController::class, 'updateWeek'])->name('course-weeks.update');
        Route::delete('/course-weeks/{week}', [CourseWeekController::class, 'destroyWeek'])->name('course-weeks.destroy');
        Route::post('/course-weeks/{week}/sessions', [CourseWeekController::class, 'storeSession'])->name('course-weeks.sessions.store');
        Route::put('/course-sessions/{session}', [CourseWeekController::class, 'updateSession'])->name('course-sessions.update');
        Route::delete('/course-sessions/{session}', [CourseWeekController::class, 'destroySession'])->name('course-sessions.destroy');
        Route::put('/course-session-items/{item}', [CourseWeekController::class, 'updateItem'])->name('course-session-items.update');

        Route::get('/demo-tasks', [DemoTaskController::class, 'index'])->name('demo-tasks.index');
        Route::get('/demo-tasks/create', [DemoTaskController::class, 'createPage'])->name('demo-tasks.create-page');
        Route::get('/demo-tasks/assign', [DemoTaskController::class, 'assignPage'])->name('demo-tasks.assign-page');
        Route::get('/demo-tasks/submissions', [DemoTaskController::class, 'submissionsPage'])->name('demo-tasks.submissions-page');
        Route::post('/demo-tasks', [DemoTaskController::class, 'store'])->name('demo-tasks.store');
        Route::put('/demo-tasks/{demoTask}', [DemoTaskController::class, 'update'])->name('demo-tasks.update');
        Route::delete('/demo-tasks/{demoTask}', [DemoTaskController::class, 'destroy'])->name('demo-tasks.destroy');
        Route::post('/demo-tasks/assign', [DemoTaskController::class, 'assign'])->name('demo-tasks.assign');
        Route::put('/demo-tasks/assign/{assignment}', [DemoTaskController::class, 'updateAssignment'])->name('demo-tasks.assignments.update');
        Route::delete('/demo-tasks/assign/{assignment}', [DemoTaskController::class, 'destroyAssignment'])->name('demo-tasks.assignments.destroy');
        Route::get('/demo-feature-video', [DemoFeatureVideoController::class, 'index'])->name('demo-feature-video.index');
        Route::post('/demo-feature-video', [DemoFeatureVideoController::class, 'store'])->name('demo-feature-video.store');
        Route::put('/demo-feature-video/{video}', [DemoFeatureVideoController::class, 'update'])->name('demo-feature-video.update');
        Route::delete('/demo-feature-video/{video}', [DemoFeatureVideoController::class, 'destroy'])->name('demo-feature-video.destroy');
        Route::get('/demo-review-videos', [DemoReviewVideoController::class, 'index'])->name('demo-review-videos.index');
        Route::post('/demo-review-videos', [DemoReviewVideoController::class, 'store'])->name('demo-review-videos.store');
        Route::put('/demo-review-videos/{video}', [DemoReviewVideoController::class, 'update'])->name('demo-review-videos.update');
        Route::delete('/demo-review-videos/{video}', [DemoReviewVideoController::class, 'destroy'])->name('demo-review-videos.destroy');
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
    Route::get('/course-session-items/{item}/download', [CourseMediaController::class, 'download'])->name('course-session-items.media.download');

    Route::middleware('role:trainer')->group(function (): void {
        Route::get('/trainer/progress', [TrainerProgressController::class, 'index'])->name('trainer.progress');
        Route::get('/trainer/assigned-students', [TrainerProgressController::class, 'assignedStudents'])->name('trainer.assigned-students');
        Route::get('/trainer/courses', [TrainerProgressController::class, 'courses'])->name('trainer.courses');
        Route::get('/trainer/submissions', [CourseItemSubmissionController::class, 'trainerIndex'])->name('trainer.submissions');
        Route::get('/trainer/courses/{course}/items', [TrainerCourseItemsController::class, 'index'])->name('trainer.courses.items');
        Route::get('/trainer/items/{item}/submissions', [CourseItemSubmissionController::class, 'itemSubmissions'])->name('trainer.items.submissions');
        Route::post('/trainer/items/{item}/quiz-live', [CourseItemSubmissionController::class, 'toggleQuizLive'])->name('trainer.items.quiz-live');
    });

    Route::middleware('role:student')->group(function (): void {
        Route::get('/my-courses', [CourseEnrollmentController::class, 'myCourses'])->name('student.courses');
        Route::get('/my-courses/{course}', [CourseEnrollmentController::class, 'showEnrolledCourse'])->name('student.courses.show');
        Route::get('/my-history', [CourseEnrollmentController::class, 'history'])->name('student.history');
        Route::get('/my-certificates', [CourseEnrollmentController::class, 'certificates'])->name('student.certificates');
        Route::get('/my-certificates/{enrollment}/download', [CourseEnrollmentController::class, 'downloadCertificate'])->name('student.certificates.download');
        Route::get('/my-certificates/{enrollment}/download-pdf', [CourseEnrollmentController::class, 'downloadCertificatePdf'])->name('student.certificates.download.pdf');
        Route::post('/course-session-items/{item}/submit', [CourseItemSubmissionController::class, 'store'])->name('course-session-items.submit');
    });

    Route::middleware('role:demo')->group(function (): void {
        Route::post('/demo-assignments/{assignment}/submit', [DemoTaskController::class, 'submit'])->name('demo-assignments.submit');
    });

    Route::get('/demo-task-submissions/{submission}/download', [DemoTaskController::class, 'download'])
        ->name('demo-tasks.submissions.download');

    Route::get('/course-item-submissions/{submission}/download', [CourseItemSubmissionController::class, 'download'])->name('course-item-submissions.download');
});
