<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseSessionItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrainerCourseItemsController extends Controller
{
    public function index(Request $request, Course $course): View
    {
        $trainer = $request->user();
        abort_unless($trainer?->role === User::ROLE_TRAINER, 403);

        $isAssigned = CourseEnrollment::where('course_id', $course->id)
            ->where('trainer_id', $trainer->id)
            ->exists();

        abort_unless($isAssigned, 403);

        $course->load(['weeks.sessions.items']);

        $items = CourseSessionItem::whereHas('session.week', fn ($q) => $q->where('course_id', $course->id))
            ->whereIn('item_type', [CourseSessionItem::TYPE_TASK, CourseSessionItem::TYPE_QUIZ])
            ->with('session.week')
            ->get()
            ->sortBy(function (CourseSessionItem $item): string {
                $week = (int) ($item->session?->week?->week_number ?? 0);
                $session = (int) ($item->session?->session_number ?? 0);
                return sprintf('%03d-%03d-%06d', $week, $session, $item->id);
            })
            ->values();

        return view('trainer.course-items', [
            'course' => $course,
            'items' => $items,
        ]);
    }
}
