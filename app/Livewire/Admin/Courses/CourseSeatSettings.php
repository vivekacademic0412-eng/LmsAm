<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseSettings;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class CourseSeatSettings extends Component
{
    public $categories = [];
    public $category_id = null;
    public $course_id = null;

    public $show_seats_as_full = false;
    public $total_seats = null;
    public $booked_seats = null;

    // Day-0 countdown, since it's set alongside seat/availability info
    public $zero_day_countdown_enabled = true;
    public $countdown_days = 0;

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
    }

    public function updatedCourseId($value): void
    {
        if (!$value) return;

        $settings = CourseSettings::firstWhere('course_id', $value);

        $this->show_seats_as_full = (bool) optional($settings)->show_seats_as_full;
        $this->total_seats = optional($settings)->total_seats;
        $this->booked_seats = optional($settings)->booked_seats;
        $this->zero_day_countdown_enabled = optional($settings)->zero_day_countdown_enabled ?? true;
        $this->countdown_days = optional($settings)->countdown_days ?? 0;
    }

    public function save(): void
    {
        $this->validate([
            'course_id'      => 'required|exists:courses,id',
            'total_seats'    => 'nullable|integer|min:0',
            'booked_seats'   => 'nullable|integer|min:0',
            'countdown_days' => 'required|integer|min:0',
        ]);

        CourseSettings::updateOrCreate(
            ['course_id' => $this->course_id],
            [
                'show_seats_as_full'         => $this->show_seats_as_full,
                'total_seats'                => $this->total_seats,
                'booked_seats'               => $this->booked_seats,
                'zero_day_countdown_enabled' => $this->zero_day_countdown_enabled,
                'countdown_days'             => $this->countdown_days,
            ]
        );

        session()->flash('success', 'Seat & availability settings saved.');
    }

    public function render()
    {
        return view('livewire.admin.courses.course-seat-settings');
    }
}