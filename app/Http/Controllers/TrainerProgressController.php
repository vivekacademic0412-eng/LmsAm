<?php

namespace App\Http\Controllers;

use App\Models\CourseSessionItem;
use App\Models\CourseEnrollment;
use App\Models\CourseCategory;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrainerProgressController extends Controller
{
    public function index(Request $request): View
    {
        $trainer = $request->user();
        abort_unless($trainer?->role === User::ROLE_TRAINER, 403);

        $categoryId = $request->query('category_id');
        $courseId = $request->query('course_id');
        $studentSearch = trim((string) $request->query('student'));

        $enrollments = CourseEnrollment::with(['course.days.items', 'student', 'progressItems'])
            ->where('trainer_id', $trainer->id)
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->whereHas('course', function ($cq) use ($categoryId) {
                    $cq->where('category_id', $categoryId)
                        ->orWhere('subcategory_id', $categoryId);
                });
            })
            ->when($courseId, fn ($q) => $q->where('course_id', $courseId))
            ->when($studentSearch !== '', function ($q) use ($studentSearch) {
                $q->whereHas('student', function ($studentQuery) use ($studentSearch) {
                    $studentQuery
                        ->where('name', 'like', '%'.$studentSearch.'%')
                        ->orWhere('email', 'like', '%'.$studentSearch.'%');
                });
            })
            ->latest('id')
            ->paginate(8)
            ->withQueryString();

        $rows = $enrollments->getCollection()->map(function (CourseEnrollment $enrollment) {
            $totalItems = CourseSessionItem::whereHas('session.week', fn ($q) => $q->where('course_id', $enrollment->course_id))->count();
            $completedItems = $enrollment->progressItems->whereNotNull('completed_at')->count();
            $progressPercent = $totalItems > 0 ? (int) floor(($completedItems / $totalItems) * 100) : 0;

            return [
                'enrollment' => $enrollment,
                'total_items' => $totalItems,
                'completed_items' => $completedItems,
                'progress_percent' => $progressPercent,
            ];
        });

        $enrollments->setCollection($rows);

        return view('trainer.progress', [
            'rows' => $enrollments,
            'categories' => CourseCategory::with('children:id,name,parent_id')
                ->whereNull('parent_id')
                ->orderBy('name')
                ->get(['id', 'name']),
            'courses' => Course::whereHas('enrollments', fn ($q) => $q->where('trainer_id', $trainer->id))
                ->orderBy('title')
                ->get(['id', 'title']),
            'activeCategoryId' => $categoryId,
            'activeCourseId' => $courseId,
            'activeStudentSearch' => $studentSearch,
        ]);
    }

    public function assignedStudents(Request $request): View
    {
        $trainer = $request->user();
        abort_unless($trainer?->role === User::ROLE_TRAINER, 403);

        $categoryId = $request->query('category_id');
        $courseId = $request->query('course_id');
        $studentSearch = trim((string) $request->query('student'));

        $enrollments = CourseEnrollment::with(['course.category', 'course.subcategory', 'student', 'assignedBy'])
            ->where('trainer_id', $trainer->id)
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->whereHas('course', function ($cq) use ($categoryId) {
                    $cq->where('category_id', $categoryId)
                        ->orWhere('subcategory_id', $categoryId);
                });
            })
            ->when($courseId, fn ($q) => $q->where('course_id', $courseId))
            ->when($studentSearch !== '', function ($q) use ($studentSearch) {
                $q->whereHas('student', function ($studentQuery) use ($studentSearch) {
                    $studentQuery
                        ->where('name', 'like', '%'.$studentSearch.'%')
                        ->orWhere('email', 'like', '%'.$studentSearch.'%');
                });
            })
            ->latest('id')
            ->paginate(8)
            ->withQueryString();

        return view('trainer.assigned-students', [
            'enrollments' => $enrollments,
            'categories' => CourseCategory::with('children:id,name,parent_id')
                ->whereNull('parent_id')
                ->orderBy('name')
                ->get(['id', 'name']),
            'courses' => Course::whereHas('enrollments', fn ($q) => $q->where('trainer_id', $trainer->id))
                ->orderBy('title')
                ->get(['id', 'title']),
            'activeCategoryId' => $categoryId,
            'activeCourseId' => $courseId,
            'activeStudentSearch' => $studentSearch,
        ]);
    }

    public function courses(Request $request): View
    {
        $trainer = $request->user();
        abort_unless($trainer?->role === User::ROLE_TRAINER, 403);

        $categories = CourseCategory::with([
            'courses' => fn ($q) => $q->with(['category', 'subcategory'])->orderBy('title'),
            'children.courses' => fn ($q) => $q->with(['category', 'subcategory'])->orderBy('title'),
        ])
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        $assignedCourseIds = CourseEnrollment::where('trainer_id', $trainer->id)->pluck('course_id')->all();
        $assignedCourses = Course::whereIn('id', $assignedCourseIds)->orderBy('title')->get();

        return view('trainer.courses', [
            'categories' => $categories,
            'assignedCourseIds' => $assignedCourseIds,
            'assignedCourses' => $assignedCourses,
        ]);
    }
}
