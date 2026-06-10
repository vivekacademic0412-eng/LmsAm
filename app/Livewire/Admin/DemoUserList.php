<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Course;
use App\Models\DemoUser;
use App\Models\SubmittedDemos;
use App\Models\EducationLevel;

class DemoUserList extends Component
{
    use WithPagination;

    public $search = '';
    public $educationLevel = '';
    public $courseFilter = '';
    public $perPage = 10;

    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search',
        'educationLevel',
        'courseFilter'
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingEducationLevel()
    {
        $this->resetPage();
    }

    public function updatingCourseFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection =
                $this->sortDirection === 'asc'
                    ? 'desc'
                    : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $query = DemoUser::with([
            'educationLevel',
            'course',
            'submittedDemos'
        ]);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('email_phone', 'like', '%' . $this->search . '%')
                    ->orWhere('ip_address', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->educationLevel) {
            $query->where('education_level_id', $this->educationLevel);
        }

        if ($this->courseFilter) {
            $query->where('preferred_course_id', $this->courseFilter);
        }

        $query->orderBy(
            $this->sortField,
            $this->sortDirection
        );

        return view('livewire.admin.demo-user-list', [
            'demoUsers' => $query->paginate($this->perPage),

            'educationLevels' => EducationLevel::orderBy('sort_order')->get(),

            'courses' => Course::orderBy('title')->get(),

            'totalUsers' => DemoUser::count(),

            'completedUsers' => DemoUser::where('progress_demo', 100)->count(),

            'totalDemos' => SubmittedDemos::count(),

            'totalCourses' => Course::count(),
        ]);
    }
}
?>
