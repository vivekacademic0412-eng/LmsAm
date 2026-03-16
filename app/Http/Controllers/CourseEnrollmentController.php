<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSessionItem;
use App\Models\CourseEnrollment;
use App\Models\CourseProgress;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourseEnrollmentController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeManager($request);

        return view('enrollments.index', [
            'enrollments' => CourseEnrollment::with(['course', 'student', 'trainer', 'assignedBy'])->latest()->get(),
            'courses' => Course::orderBy('title')->get(),
            'students' => User::where('role', User::ROLE_STUDENT)->orderBy('name')->get(),
            'trainers' => User::where('role', User::ROLE_TRAINER)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeManager($request);

        $data = $request->validate([
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'student_id' => ['required', 'integer', Rule::exists('users', 'id')->where('role', User::ROLE_STUDENT)],
            'trainer_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where('role', User::ROLE_TRAINER)],
        ]);

        CourseEnrollment::updateOrCreate(
            [
                'course_id' => $data['course_id'],
                'student_id' => $data['student_id'],
            ],
            [
                'trainer_id' => $data['trainer_id'] ?? null,
                'assigned_by' => $request->user()->id,
            ]
        );

        return back()->with('success', 'Enrollment assigned.');
    }

    public function destroy(Request $request, CourseEnrollment $enrollment): RedirectResponse
    {
        $this->authorizeManager($request);
        $enrollment->delete();

        return back()->with('success', 'Enrollment removed.');
    }

    public function myCourses(Request $request): View
    {
        $user = $request->user();
        abort_unless($user?->role === User::ROLE_STUDENT, 403);

        return view('student.courses', [
            'categories' => \App\Models\CourseCategory::with('courses')->orderBy('name')->get(),
            'enrolledCourseIds' => $user->enrollmentsAsStudent()->pluck('course_id')->all(),
        ]);
    }

    public function showEnrolledCourse(Request $request, Course $course): View
    {
        $user = $request->user();
        abort_unless($user?->role === User::ROLE_STUDENT, 403);

        $enrollment = CourseEnrollment::with(['course.weeks.sessions.items'])
            ->where('course_id', $course->id)
            ->where('student_id', $user->id)
            ->first();

        abort_unless($enrollment, 403, 'You can open only enrolled courses.');

        $itemIds = CourseSessionItem::whereHas('session.week', fn ($q) => $q->where('course_id', $course->id))->pluck('id');

        foreach ($itemIds as $itemId) {
            CourseProgress::updateOrCreate(
                [
                    'course_enrollment_id' => $enrollment->id,
                    'course_session_item_id' => $itemId,
                ],
                [
                    'completed_at' => now(),
                ]
            );
        }

        $totalItems = $itemIds->count();
        $completedItems = $enrollment->progressItems()->whereNotNull('completed_at')->count();
        $enrollment = $enrollment->fresh(['course.weeks.sessions.items']);

        return view('student.course-show', [
            'enrollment' => $enrollment,
            'totalItems' => $totalItems,
            'completedItems' => $completedItems,
        ]);
    }

    private function authorizeManager(Request $request): void
    {
        abort_unless(
            in_array($request->user()?->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true),
            403
        );
    }

}
