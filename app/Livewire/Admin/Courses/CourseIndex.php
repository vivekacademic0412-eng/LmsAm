<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseEnrollment;
use App\Models\CourseLevel;
use App\Models\CourseType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Services\CrashCourseSyncService;

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
    public $course_level_id;
    public $course_type_id;
    public $language;
    public $duration_hours;
    public $short_description;
    public $description;
    public $original_price;
    public $price;
    public $gst;
    public $thumbnail;           // newly-picked file (temp upload), only set when user chooses a new image
    public $existing_thumbnail;  // absolute URL of the current image, used to render a preview while editing

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

    public function getLevelsProperty()
    {
        return CourseLevel::orderBy('name')->get(['id', 'name']);
    }

    public function getTypesProperty()
    {
        return CourseType::orderBy('name')->get(['id', 'name']);
    }

    public function getAssignedCourseIdsProperty()
    {
        if (!$this->isTrainer) return [];

        return CourseEnrollment::where('trainer_id', Auth::id())->pluck('course_id')->all();
    }

    public function updatingCategoryId(): void
    {
        $this->resetPage();
    }
    public function updatingSubcategoryId(): void
    {
        $this->resetPage();
    }

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

    protected function rules(): array
    {
        return [
            'title'               => 'required|string|max:255',
            'form_category_id'    => 'required|exists:course_categories,id',
            'form_subcategory_id' => 'nullable|exists:course_categories,id',
            'course_level_id'     => 'nullable|exists:course_levels,id',
            'course_type_id'      => 'nullable|exists:course_types,id',
            'language'            => 'nullable|string|max:100',
            'duration_hours'      => 'required|integer|min:1',
            'short_description'   => 'nullable|string',
            'description'         => 'nullable|string',
            'original_price'      => 'nullable|numeric|min:0',
            'price'               => 'nullable|numeric|min:0',
            'gst'                 => 'nullable|numeric|min:0',
            'thumbnail'           => 'nullable|image|max:2048',
        ];
    }

    public function saveCourse(): void
    {
        $this->validate($this->rules());

        $thumbnailPath = null;
        if ($this->thumbnail) {
            $thumbnailPath = $this->thumbnail->store('course-thumbnails', 'public');
        }

         $course=Course::create([
            'category_id'        => $this->form_category_id,
            'subcategory_id'     => $this->form_subcategory_id ?: null,
            'course_level_id'    => $this->course_level_id ?: null,
            'course_type_id'     => $this->course_type_id ?: null,
            'title'              => $this->title,
            'slug'                => \Illuminate\Support\Str::slug($this->title) . '-' . uniqid(),
            'language'           => $this->language,
            'duration_hours'     => $this->duration_hours,
            'short_description'  => $this->short_description,
            'description'        => $this->description,
            'original_price'     => $this->original_price ?: 0,
            'price'              => $this->price ?: 0,
            'gst'                => $this->gst ?: 0,
            'thumbnail'          => $thumbnailPath,
            'created_by'         => Auth::id(),
        ]);
        app(CrashCourseSyncService::class)->syncFromParent($course->fresh(['category', 'courseType', 'courseLevel', 'settings']));
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
        $this->course_level_id = $course->course_level_id;
        $this->course_type_id = $course->course_type_id;
        $this->language = $course->language;
        $this->duration_hours = $course->duration_hours;
        $this->short_description = $course->short_description;
        $this->description = $course->description;
        $this->original_price = $course->original_price;
        $this->price = $course->price;
        $this->gst = $course->gst;
        $this->existing_thumbnail = $course->thumbnail_url; // for preview only
        $this->thumbnail = null;                             // no new file picked yet
    }

    public function closeEditModal(): void
    {
        $this->editingCourseId = null;
        $this->resetForm();
    }

    public function updateCourse(): void
    {
        $this->validate($this->rules());

        $course = Course::findOrFail($this->editingCourseId);

        $data = [
            'category_id'        => $this->form_category_id,
            'subcategory_id'     => $this->form_subcategory_id ?: null,
            'course_level_id'    => $this->course_level_id ?: null,
            'course_type_id'     => $this->course_type_id ?: null,
            'title'              => $this->title,
            'language'           => $this->language,
            'duration_hours'     => $this->duration_hours,
            'short_description'  => $this->short_description,
            'description'        => $this->description,
            'original_price'     => $this->original_price ?: 0,
            'price'              => $this->price ?: 0,
            'gst'                => $this->gst ?: 0,
        ];

        // Only touch the thumbnail column if the user actually picked a new file.
        if ($this->thumbnail) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $data['thumbnail'] = $this->thumbnail->store('course-thumbnails', 'public');
        }

        $course->update($data);
        app(CrashCourseSyncService::class)->syncFromParent($course->fresh(['category', 'courseType', 'courseLevel', 'settings']));
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
            'title',
            'form_category_id',
            'form_subcategory_id',
            'course_level_id',
            'course_type_id',
            'language',
            'duration_hours',
            'short_description',
            'description',
            'original_price',
            'price',
            'gst',
            'thumbnail',
            'existing_thumbnail',
        ]);
    }

    public function render()
    {
        $courses = Course::with(['category', 'subcategory', 'creator'])
            ->when($this->category_id, fn($q) => $q->where('category_id', $this->category_id))
            ->when($this->subcategory_id, fn($q) => $q->where('subcategory_id', $this->subcategory_id))
            ->latest()
            ->paginate(20);

        return view('livewire.admin.courses.course-index', [
            'courses'    => $courses,
            'categories' => $this->categories,
            'levels'     => $this->levels,
            'types'      => $this->types,
        ]);
    }
}
