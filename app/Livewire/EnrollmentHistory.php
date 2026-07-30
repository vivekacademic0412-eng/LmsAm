<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseEnrollment;
use App\Models\User;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class EnrollmentHistory extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $categoryFilter = '';

    #[Url(history: true)]
    public $subcategoryFilter = '';

    #[Url(history: true)]
    public $courseFilter = '';

    #[Url(history: true)]
    public $trainerFilter = '';

    #[Url(history: true)]
    public $sort = 'latest';

    public $showFilters = false;

    protected string $paginationTheme = 'custom';

    public function mount(): void
    {
        abort_unless(auth()->user()?->role === User::ROLE_STUDENT, 403);
    }

    /* ── Reactivity ──────────────────────────────────────── */
    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingCategoryFilter(): void { $this->subcategoryFilter = ''; $this->resetPage(); }
    public function updatingSubcategoryFilter(): void { $this->resetPage(); }
    public function updatingCourseFilter(): void { $this->resetPage(); }
    public function updatingTrainerFilter(): void { $this->resetPage(); }
    public function updatingSort(): void { $this->resetPage(); }

    public function toggleFilters(): void
    {
        $this->showFilters = ! $this->showFilters;
    }

    public function setSort(string $sort): void
    {
        $this->sort = $sort;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'categoryFilter', 'subcategoryFilter', 'courseFilter', 'trainerFilter']);
        $this->sort = 'latest';
        $this->showFilters = false;
        $this->resetPage();
    }

    /* ── Query building ──────────────────────────────────── */
    private function baseQuery()
    {
        return CourseEnrollment::query()
            ->where('student_id', auth()->id())
            ->when($this->search, fn ($q) => $q->whereHas(
                'course',
                fn ($cq) => $cq->where('title', 'like', '%' . $this->search . '%')
            ))
            ->when($this->categoryFilter, fn ($q) => $q->whereHas(
                'course',
                fn ($cq) => $cq->where('category_id', $this->categoryFilter)
            ))
            ->when($this->subcategoryFilter, fn ($q) => $q->whereHas(
                'course',
                fn ($cq) => $cq->where('subcategory_id', $this->subcategoryFilter)
            ))
            ->when($this->courseFilter, fn ($q) => $q->where('course_id', $this->courseFilter))
            ->when($this->trainerFilter, fn ($q) => $q->where('trainer_id', $this->trainerFilter));
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
        $studentId = auth()->id();

        $enrollments = $this->baseQuery()
            ->with(['course.category', 'course.subcategory', 'trainer', 'assignedBy'])
            ->when($this->sort === 'oldest', fn ($q) => $q->oldest('id'), fn ($q) => $q->latest('id'))
            ->paginate(8);

        $enrolledCourseIds = CourseEnrollment::where('student_id', $studentId)->pluck('course_id');
        $enrolledTrainerIds = CourseEnrollment::where('student_id', $studentId)->whereNotNull('trainer_id')->pluck('trainer_id');

        return view('livewire.enrollment-history', [
            'enrollments' => $enrollments,
            'stats' => [
                'total'    => $this->baseQuery()->count(),
                'courses'  => (clone $this->baseQuery())->distinct('course_id')->count('course_id'),
                'trainers' => (clone $this->baseQuery())->whereNotNull('trainer_id')->distinct('trainer_id')->count('trainer_id'),
            ],
            'courses'    => Course::whereIn('id', $enrolledCourseIds)->orderBy('title')->get(),
            'trainers'   => User::where('role', User::ROLE_TRAINER)->whereIn('id', $enrolledTrainerIds)->orderBy('name')->get(),
            'categories' => CourseCategory::with('children:id,name,parent_id')
                ->whereNull('parent_id')
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }
}