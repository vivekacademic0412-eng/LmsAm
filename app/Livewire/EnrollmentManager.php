<?php

namespace App\Livewire;

use App\Concerns\SendsCourseAssignmentComms;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class EnrollmentManager extends Component
{
    use WithPagination;
    use SendsCourseAssignmentComms;

    /* ── Filters ─────────────────────────────────────────── */
    public $categoryFilter = '';
    public $subcategoryFilter = '';
    public $courseFilter = '';
    public $trainerFilter = '';
    public $showFilterPanel = false;

    /* ── Form state ──────────────────────────────────────── */
    public $enrollmentId = null;
    public $course_id = '';
    public $student_id = '';
    public $trainer_id = '';

    public $showCreateModal = false;
    public $showEditModal = false;

    protected string $paginationTheme = 'custom';

    public function mount(): void
    {
        // $this->authorizeManager();
    }

    /* ── Validation ──────────────────────────────────────── */
    protected function rules(): array
    {
        return [
            'course_id'  => ['required', 'integer', 'exists:courses,id'],
            'student_id' => ['required', 'integer', Rule::exists('users', 'id')->where('role', User::ROLE_STUDENT)],
            'trainer_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where('role', User::ROLE_TRAINER)],
        ];
    }

    protected function messages(): array
    {
        return [
            'course_id.required'  => 'Please select a course.',
            'course_id.exists'    => 'Selected course is invalid.',
            'student_id.required' => 'Please select a student.',
            'student_id.exists'   => 'Selected student is invalid.',
            'trainer_id.exists'   => 'Selected trainer is invalid.',
        ];
    }

    /**
     * Mirrors the controller's second validate() call: the (course, student)
     * pair must be unique, scoped by student, ignoring the current row on update.
     */
    private function assertUniqueAssignment(array $data, ?int $ignoreId = null): void
    {
        Validator::make($data, [
            'course_id' => [
                Rule::unique('course_enrollments', 'course_id')
                    ->where(fn ($q) => $q->where('student_id', $data['student_id']))
                    ->ignore($ignoreId),
            ],
        ], [
            'course_id.unique' => 'This student is already enrolled in the selected course.',
        ])->validate();
    }

    /* ── Filter reactivity ───────────────────────────────── */
    public function updatingCategoryFilter(): void
    {
        $this->subcategoryFilter = '';
        $this->resetPage();
    }

    public function updatingSubcategoryFilter(): void { $this->resetPage(); }
    public function updatingCourseFilter(): void { $this->resetPage(); }
    public function updatingTrainerFilter(): void { $this->resetPage(); }

    public function toggleFilterPanel(): void
    {
        $this->showFilterPanel = ! $this->showFilterPanel;
    }

    public function clearFilters(): void
    {
        $this->reset(['categoryFilter', 'subcategoryFilter', 'courseFilter', 'trainerFilter']);
        $this->showFilterPanel = false;
        $this->resetPage();
    }

    /* ── Modal handling ──────────────────────────────────── */
    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->resetErrorBag();
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetForm();
        $this->resetErrorBag();
    }

    public function openEditModal(int $id): void
    {
        $enrollment = CourseEnrollment::findOrFail($id);

        $this->enrollmentId = $enrollment->id;
        $this->course_id    = $enrollment->course_id;
        $this->student_id   = $enrollment->student_id;
        $this->trainer_id   = $enrollment->trainer_id;

        $this->resetErrorBag();
        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->resetForm();
        $this->resetErrorBag();
    }

    private function resetForm(): void
    {
        $this->reset(['enrollmentId', 'course_id', 'student_id', 'trainer_id']);
    }

    /* ── CRUD ────────────────────────────────────────────── */
    public function store(): void
    {
        // $this->authorizeManager();

        $data = $this->validate();
        $this->assertUniqueAssignment($data);

        $enrollment = CourseEnrollment::create([
            'course_id'   => $data['course_id'],
            'student_id'  => $data['student_id'],
            'trainer_id'  => $data['trainer_id'] ?? null,
            'assigned_by' => auth()->id(),
        ])->fresh(['course.category', 'student', 'trainer']);

        $emailSent = $this->sendCourseAssignmentEmail($enrollment, auth()->user(), false);
        $notificationSent = $this->sendCourseAssignmentNotification($enrollment, auth()->user(), false);

        $this->showCreateModal = false;
        $this->resetForm();
        $this->resetPage();

        $message = $this->buildAssignmentSuccessMessage('Enrollment assigned.', $emailSent, $notificationSent);

        if (! $emailSent) {
            $this->addError('mail', 'Course assignment email could not be sent. Check mail configuration to deliver the course details.');
        }

        $this->dispatch('toast', type: $emailSent ? 'success' : 'warning', message: $message);
    }

    public function update(): void
    {
        // $this->authorizeManager();

        $data = $this->validate();
        $this->assertUniqueAssignment($data, $this->enrollmentId);

        $enrollment = CourseEnrollment::findOrFail($this->enrollmentId);

        $enrollment->update([
            'course_id'   => $data['course_id'],
            'student_id'  => $data['student_id'],
            'trainer_id'  => $data['trainer_id'] ?? null,
            'assigned_by' => auth()->id(),
        ]);

        $enrollment = $enrollment->fresh(['course.category', 'student', 'trainer']);

        $emailSent = $this->sendCourseAssignmentEmail($enrollment, auth()->user(), true);
        $notificationSent = $this->sendCourseAssignmentNotification($enrollment, auth()->user(), true);

        $this->showEditModal = false;
        $this->resetForm();

        $message = $this->buildAssignmentSuccessMessage('Enrollment updated.', $emailSent, $notificationSent);

        if (! $emailSent) {
            $this->addError('mail', 'Course assignment email could not be sent. Check mail configuration to deliver the course details.');
        }

        $this->dispatch('toast', type: $emailSent ? 'success' : 'warning', message: $message);
    }

    #[On('delete-enrollment')]
    public function delete(int $id): void
    {
        // $this->authorizeManager();

        $enrollment = CourseEnrollment::find($id);

        if ($enrollment) {
            $enrollment->delete();
            $this->resetPage();
            $this->dispatch('toast', type: 'success', message: 'Enrollment removed.');
        }
    }

    #[On('resend-enrollment-email')]
    public function resendEmail(int $id): void
    {
        // $this->authorizeManager();

        $enrollment = CourseEnrollment::with(['course.category', 'student', 'trainer'])->find($id);

        if (! $enrollment) {
            return;
        }

        $mailSent = $this->sendCourseAssignmentEmail($enrollment, null, false, true);
        $notificationSent = $this->sendCourseAssignmentNotification($enrollment, null, false, true);

        $message = $mailSent
            ? ($notificationSent
                ? 'Course access email resent successfully and dashboard notification sent.'
                : 'Course access email resent successfully.')
            : ($notificationSent
                ? 'Course access email could not be resent, but the dashboard notification was sent.'
                : 'Course access email could not be resent.');

        if (! $mailSent) {
            $this->addError('mail', 'Course access email could not be resent. Check mail configuration and try again.');
        }

        $this->dispatch('toast', type: $mailSent ? 'success' : 'error', message: $message);
    }

    /* ── Computed data ───────────────────────────────────── */
    public function getSubcategoryOptionsProperty()
    {
        $categories = CourseCategory::with('children:id,name,parent_id')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        if (! $this->categoryFilter) {
            return $categories->flatMap(fn ($c) => $c->children->map(fn ($s) => (object) [
                'id' => $s->id, 'label' => "{$c->name} / {$s->name}",
            ]));
        }

        $category = $categories->firstWhere('id', (int) $this->categoryFilter);

        return $category
            ? $category->children->map(fn ($s) => (object) ['id' => $s->id, 'label' => "{$category->name} / {$s->name}"])
            : collect();
    }

    public function render()
    {
        $enrollments = CourseEnrollment::with(['course.category', 'course.subcategory', 'student', 'trainer', 'assignedBy'])
            ->when($this->categoryFilter, fn ($q) => $q->whereHas('course', fn ($cq) => $cq->where('category_id', $this->categoryFilter)))
            ->when($this->subcategoryFilter, fn ($q) => $q->whereHas('course', fn ($cq) => $cq->where('subcategory_id', $this->subcategoryFilter)))
            ->when($this->trainerFilter, fn ($q) => $q->where('trainer_id', $this->trainerFilter))
            ->when($this->courseFilter, fn ($q) => $q->where('course_id', $this->courseFilter))
            ->latest()
            ->paginate(8);

        return view('livewire.enrollment-manager', [
            'enrollments' => $enrollments,
            'courses'     => Course::orderBy('title')->get(),
            'students'    => User::where('role', User::ROLE_STUDENT)->orderBy('name')->get(),
            'trainers'    => User::where('role', User::ROLE_TRAINER)->orderBy('name')->get(),
            'categories'  => CourseCategory::with('children:id,name,parent_id')
                ->whereNull('parent_id')
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }
}