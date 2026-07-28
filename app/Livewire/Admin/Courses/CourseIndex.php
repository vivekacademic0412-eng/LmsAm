<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseEnrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class CourseIndex extends Component
{
    use WithPagination, WithFileUploads;

    // ── Filters ──
    public $category_id = '';
    public $subcategory_id = '';
    public $showFilterPanel = false;

    // ── Permissions (set these based on your existing auth logic) ──
    public $canManage = false;
    public $isTrainer = false;

    // ── Modals ──
    public $showCreateModal = false;
    public $editingCourseId = null;
    public $deletingCourseId = null;

    // ── Form fields ──
    public $title;
    public $form_category_id;
    public $form_subcategory_id;
    public $language;
    public $duration_hours;
    public $short_description;
    public $description;
    public $thumbnail; // uploaded file

    public function mount(): void
    {
        $user = Auth::user();
        $this->canManage = in_array($user->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN], true);
        $this->isTrainer = $user->role === \App\Models\User::ROLE_TRAINER;
    }

    public function getCategoriesProperty()
    {
        return CourseCategory::with('children:id,name,parent_id')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function getAssignedCourseIdsProperty()
    {
        if (!$this->isTrainer) return [];

        return CourseEnrollment::where('trainer_id', Auth::id())->pluck('course_id')->all();
    }

    public function updatingCategoryId(): void { $this->resetPage(); }
    public function updatingSubcategoryId(): void { $this->resetPage(); }

    public function toggleFilterPanel(): void
    {
        $this->showFilterPanel = !$this->showFilterPanel;
    }

    public function clearFilters(): void
    {
        $this->category_id = '';
        $this->subcategory_id = '';
        $this->showFilterPanel = false;
        $this->resetPage();
    }

    // ── Create ──
    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function saveCourse(): void
    {
        $this->validate([
            'title'               => 'required|string|max:255',
            'form_category_id'    => 'required|exists:course_categories,id',
            'form_subcategory_id' => 'nullable|exists:course_categories,id',
            'language'            => 'nullable|string|max:100',
            'duration_hours'      => 'required|integer|min:1',
            'short_description'   => 'nullable|string',
            'description'         => 'nullable|string',
            'thumbnail'           => 'nullable|image|max:2048',
        ]);

        $thumbnailPath = null;
        if ($this->thumbnail) {
            $thumbnailPath = $this->thumbnail->store('course-thumbnails', 'public');
        }

        Course::create([
            'category_id'        => $this->form_category_id,
            'subcategory_id'     => $this->form_subcategory_id ?: null,
            'title'              => $this->title,
            'language'           => $this->language,
            'duration_hours'     => $this->duration_hours,
            'short_description'  => $this->short_description,
            'description'        => $this->description,
            'thumbnail'          => $thumbnailPath,
            'created_by'         => Auth::id(),
        ]);

        session()->flash('success', 'Course created successfully.');
        $this->closeCreateModal();
    }

    // ── Edit ──
    public function openEditModal($courseId): void
    {
        $course = Course::findOrFail($courseId);

        $this->editingCourseId = $course->id;
        $this->title = $course->title;
        $this->form_category_id = $course->category_id;
        $this->form_subcategory_id = $course->subcategory_id;
        $this->language = $course->language;
        $this->duration_hours = $course->duration_hours;
        $this->short_description = $course->short_description;
        $this->description = $course->description;
        $this->thumbnail = null;
    }

    public function closeEditModal(): void
    {
        $this->editingCourseId = null;
        $this->resetForm();
    }

    public function updateCourse(): void
    {
        $this->validate([
            'title'               => 'required|string|max:255',
            'form_category_id'    => 'required|exists:course_categories,id',
            'form_subcategory_id' => 'nullable|exists:course_categories,id',
            'language'            => 'nullable|string|max:100',
            'duration_hours'      => 'required|integer|min:1',
            'short_description'   => 'nullable|string',
            'description'         => 'nullable|string',
            'thumbnail'           => 'nullable|image|max:2048',
        ]);

        $course = Course::findOrFail($this->editingCourseId);

        $data = [
            'category_id'        => $this->form_category_id,
            'subcategory_id'     => $this->form_subcategory_id ?: null,
            'title'              => $this->title,
            'language'           => $this->language,
            'duration_hours'     => $this->duration_hours,
            'short_description'  => $this->short_description,
            'description'        => $this->description,
        ];

        if ($this->thumbnail) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $data['thumbnail'] = $this->thumbnail->store('course-thumbnails', 'public');
        }

        $course->update($data);

        session()->flash('success', 'Course updated successfully.');
        $this->closeEditModal();
    }

    // ── Delete ──
    public function confirmDelete($courseId): void
    {
        $this->deletingCourseId = $courseId;
    }

    public function cancelDelete(): void
    {
        $this->deletingCourseId = null;
    }

    public function deleteCourse(): void
    {
        $course = Course::findOrFail($this->deletingCourseId);

        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

        session()->flash('success', 'Course deleted.');
        $this->deletingCourseId = null;
    }

    protected function resetForm(): void
    {
        $this->reset([
            'title', 'form_category_id', 'form_subcategory_id', 'language',
            'duration_hours', 'short_description', 'description', 'thumbnail',
        ]);
    }

    public function render()
    {
        $courses = Course::with(['category', 'subcategory', 'creator'])
            ->when($this->category_id, fn ($q) => $q->where('category_id', $this->category_id))
            ->when($this->subcategory_id, fn ($q) => $q->where('subcategory_id', $this->subcategory_id))
            ->latest()
            ->paginate(20);

        return view('livewire.admin.courses.course-index', [
            'courses'    => $courses,
            'categories' => $this->categories,
        ]);
    }
}