<?php

namespace App\Http\Controllers;

use App\Models\CourseSessionItem;
use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrainerProgressController extends Controller
{
    public function index(Request $request): View
    {
        $trainer = $request->user();
        abort_unless($trainer?->role === User::ROLE_TRAINER, 403);

        $enrollments = CourseEnrollment::with(['course.days.items', 'student', 'progressItems'])
            ->where('trainer_id', $trainer->id)
            ->get();

        $rows = $enrollments->map(function (CourseEnrollment $enrollment) {
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

        return view('trainer.progress', [
            'rows' => $rows,
        ]);
    }
}
