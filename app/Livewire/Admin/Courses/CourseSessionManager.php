<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseSession;
use App\Models\CourseSessionSetting;
use App\Models\CourseWeek;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CourseSessionManager extends Component
{
    public $categories = [];
    public $category_id = null;
    public $course_id = null;
    public $week_id = null;

    public $weeks = [];
    public $sessions = [];

    // add/edit form
    public $editing_session_id = null;
    public $session_number;
    public $title;
    public $is_required_for_certificate = false;
    public $meet_link;
    public $meet_datetime;
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
        $this->week_id = null;
        $this->weeks = [];
        $this->sessions = [];
    }

    public function updatedCourseId(): void
    {
        $this->week_id = null;
        $this->sessions = [];
        $this->weeks = $this->course_id
            ? CourseWeek::where('course_id', $this->course_id)->orderBy('week_number')->get()
            : [];
    }

    public function updatedWeekId(): void
    {
        $this->loadSessions();
    }

    public function loadSessions(): void
    {
        if (!$this->week_id) {
            $this->sessions = [];
            return;
        }

        $this->sessions = CourseSession::with('settings')
            ->where('course_week_id', $this->week_id)
            ->orderBy('session_number')
            ->get();
    }

    public function editSession($sessionId): void
    {
        $session = CourseSession::with('settings')->findOrFail($sessionId);
        $this->editing_session_id = $session->id;
        $this->session_number = $session->session_number;
        $this->title = $session->title;

        $s = $session->settings;
        $this->is_required_for_certificate = (bool) optional($s)->is_required_for_certificate;
        $this->meet_link = optional($s)->meet_link;
        $this->meet_datetime = optional($s)->meet_datetime?->format('Y-m-d\TH:i');
        $this->is_visible = optional($s)->is_visible ?? true;
    }

    public function resetForm(): void
    {
        $this->editing_session_id = null;
        $this->session_number = null;
        $this->title = null;
        $this->is_required_for_certificate = false;
        $this->meet_link = null;
        $this->meet_datetime = null;
        $this->is_visible = true;
    }

    public function saveSession(): void
    {
        $this->validate([
            'week_id'        => 'required|exists:course_weeks,id',
            'session_number' => 'required|integer|min:1',
            'title'          => 'required|string|max:255',
            'meet_link'      => 'nullable|url',
            'meet_datetime'  => 'nullable|date',
        ]);

        $session = CourseSession::updateOrCreate(
            [
                'id'             => $this->editing_session_id,
                'course_week_id' => $this->week_id,
            ],
            [
                'course_week_id' => $this->week_id,
                'session_number' => $this->session_number,
                'title'          => $this->title,
            ]
        );

        CourseSessionSetting::updateOrCreate(
            ['course_session_id' => $session->id],
            [
                'is_required_for_certificate' => $this->is_required_for_certificate,
                'meet_link'                    => $this->meet_link,
                'meet_datetime'                => $this->meet_datetime,
                'is_visible'                    => $this->is_visible,
            ]
        );

        session()->flash('success', 'Session saved.');
        $this->resetForm();
        $this->loadSessions();
    }

    public function deleteSession($sessionId): void
    {
        CourseSession::where('id', $sessionId)->delete();
        session()->flash('success', 'Session deleted.');
        $this->loadSessions();
    }

    public function render()
    {
        return view('livewire.admin.courses.course-session-manager');
    }
}