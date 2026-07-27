<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseSettings;
use App\Models\CourseWeek;
use App\Models\CourseWeekSetting;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CourseCertificateSettings extends Component
{
    // Step 1: subject/course pickers
    public $category_id = null;
    public $course_id = null;

    // Course-level certificate settings
    public $certificate_mode = 'end_of_course'; // end_of_course | per_week | per_level | both
    public $min_completion_percent = 80;

    // Per-week certificate rows: [ ['id' => weekId, 'title' => .., 'certificate_enabled' => bool, 'min_completion_percent' => int|null] ]
    public $weekRows = [];

    public $categories = [];

    public function mount(): void
    {
        $this->categories = CourseCategory::orderBy('name')->get();
    }

    public function updatedCategoryId(): void
    {
        $this->course_id = null;
        $this->weekRows = [];
    }

    public function updatedCourseId($value): void
    {
        if (!$value) {
            $this->weekRows = [];
            return;
        }

        $course = Course::with('settings')->findOrFail($value);

        $settings = $course->settings ?? new CourseSettings(['min_completion_percent' => 80, 'certificate_mode' => 'end_of_course']);
        $this->certificate_mode = $settings->certificate_mode ?? 'end_of_course';
        $this->min_completion_percent = $settings->min_completion_percent ?? 80;

        $weeks = CourseWeek::with('settings')
            ->where('course_id', $course->id)
            ->orderBy('week_number')
            ->get();

        $this->weekRows = $weeks->map(function (CourseWeek $week) {
            return [
                'id'                      => $week->id,
                'week_number'             => $week->week_number,
                'title'                   => $week->title,
                'certificate_enabled'     => (bool) optional($week->settings)->certificate_enabled,
                'min_completion_percent'  => optional($week->settings)->min_completion_percent,
            ];
        })->toArray();
    }

    public function getCoursesProperty()
    {
        if (!$this->category_id) {
            return collect();
        }

        return Course::where('category_id', $this->category_id)->orderBy('title')->get();
    }

    public function save(): void
    {
        $this->validate([
            'course_id'               => 'required|exists:courses,id',
            'certificate_mode'        => 'required|in:end_of_course,per_week,per_level,both',
            'min_completion_percent'  => 'required|integer|min:1|max:100',
        ]);

        CourseSettings::updateOrCreate(
            ['course_id' => $this->course_id],
            [
                'certificate_mode'       => $this->certificate_mode,
                'min_completion_percent' => $this->min_completion_percent,
            ]
        );

        foreach ($this->weekRows as $row) {
            CourseWeekSetting::updateOrCreate(
                ['course_week_id' => $row['id']],
                [
                    'certificate_enabled'    => (bool) ($row['certificate_enabled'] ?? false),
                    'min_completion_percent' => $row['min_completion_percent'] !== '' ? $row['min_completion_percent'] : null,
                ]
            );
        }

        session()->flash('success', 'Certificate settings saved successfully.');
    }

    public function render()
    {
        return view('livewire.admin.courses.course-certificate-settings');
    }
}