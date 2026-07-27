<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseWeek;
use App\Models\CourseWeekSetting;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CourseWeekManager extends Component
{
    public $categories = [];
    public $category_id = null;
    public $course_id = null;

    public $weeks = [];

    // add/edit form
    public $editing_week_id = null;
    public $week_number;
    public $title;
    public $is_visible = true;

    public function mount(): void
    {
        $this->categories = CourseCategory::orderBy('name')->get();
    }

    public function getCoursesProperty()
    {
        if (!$this->category_id) return collect();
        return Course::where('category_id', $this->category_id)->orderBy('title')->get();
    }

    public function updatedCategoryId(): void
    {
        $this->course_id = null;
        $this->weeks = [];
    }

    public function updatedCourseId($value): void
    {
        $this->loadWeeks();
    }

    public function loadWeeks(): void
    {
        if (!$this->course_id) {
            $this->weeks = [];
            return;
        }

        $this->weeks = CourseWeek::with('settings')
            ->where('course_id', $this->course_id)
            ->orderBy('week_number')
            ->get();
    }

    public function editWeek($weekId): void
    {
        $week = CourseWeek::with('settings')->findOrFail($weekId);
        $this->editing_week_id = $week->id;
        $this->week_number = $week->week_number;
        $this->title = $week->title;
        $this->is_visible = optional($week->settings)->is_visible ?? true;
    }

    public function resetForm(): void
    {
        $this->editing_week_id = null;
        $this->week_number = null;
        $this->title = null;
        $this->is_visible = true;
    }

    public function saveWeek(): void
    {
        $this->validate([
            'course_id'   => 'required|exists:courses,id',
            'week_number' => 'required|integer|min:1',
            'title'       => 'required|string|max:255',
        ]);

        $week = CourseWeek::updateOrCreate(
            [
                'id'        => $this->editing_week_id,
                'course_id' => $this->course_id,
            ],
            [
                'course_id'   => $this->course_id,
                'week_number' => $this->week_number,
                'title'       => $this->title,
            ]
        );

        CourseWeekSetting::updateOrCreate(
            ['course_week_id' => $week->id],
            ['is_visible' => $this->is_visible]
        );

        session()->flash('success', 'Week saved.');
        $this->resetForm();
        $this->loadWeeks();
    }

    public function deleteWeek($weekId): void
    {
        CourseWeek::where('id', $weekId)->delete();
        session()->flash('success', 'Week deleted.');
        $this->loadWeeks();
    }

    public function render()
    {
        return view('livewire.admin.courses.course-week-manager');
    }
}