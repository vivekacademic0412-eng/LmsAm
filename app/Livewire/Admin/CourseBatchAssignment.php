<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Batch;
use App\Models\Course;
use App\Models\User;
use App\Models\CourseBatch;
use Illuminate\Validation\Rule;

class CourseBatchAssignment extends Component
{
    /**
     * Current course (when opened from Course Details page)
     */
    public ?Course $course = null;

    /**
     * Form Fields
     */
    public $course_id = '';
    public $batch_id = '';
    public $trainer_id = '';
    public $status = 'active';

    /**
     * Page
     */
    public $courseId = null;

    /**
     * Modal
     */
    public bool $showModal = false;
    public $editingId = null;

    /**
     * Filter
     */
    public $search = '';

    protected $listeners = [
        'refreshComponent' => '$refresh'
    ];

    /**
     * -----------------------------------------------------
     * Mount
     * -----------------------------------------------------
     */
    public function mount($courseId = null)
    {
        $this->courseId = $courseId;

        if ($courseId) {

            $this->course = Course::findOrFail($courseId);

            $this->course_id = $courseId;
        }
    }

    /**
     * -----------------------------------------------------
     * Validation
     * -----------------------------------------------------
     */
    protected function rules()
    {
        return [
            'course_id' => [
                'required',
                'exists:courses,id',
            ],

            'batch_id' => [
                'required',
                'exists:batches,id',
            ],

            'trainer_id' => [
                'required',
                'exists:users,id',
            ],

            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ];
    }

    protected $messages = [

        'course_id.required' => 'Please select course.',

        'batch_id.required' => 'Please select batch.',

        'trainer_id.required' => 'Please select trainer.',
    ];

    /**
     * -----------------------------------------------------
     * Computed Properties
     * -----------------------------------------------------
     */

    public function getCoursesProperty()
    {
        if ($this->course_id) {
            return Course::where('id', $this->course_id)->get();
        }

        return Course::orderBy('title')->get();
    }

    public function getBatchesProperty()
    {
        return Batch::orderBy('batch_code')->get();
    }

    public function getTrainersProperty()
    {
        return User::where('role', 'trainer')
            ->orderBy('name')
            ->get();
    }

    public function getAssignmentsProperty()
    {
        return CourseBatch::with([
            'course:id,title',
            'batch:id,batch_code',
            'trainer:id,name',
        ])
            ->when($this->course_id, function ($q) {
                $q->where('course_id', $this->course_id);
            })
            ->when($this->search, function ($q) {

                $q->whereHas('batch', function ($b) {
                    $b->where('batch_code', 'like', '%' . $this->search . '%');
                });

            })
            ->latest()
            ->get();
    }

    /**
     * -----------------------------------------------------
     * Open Modal
     * -----------------------------------------------------
     */
    public function create()
    {
        $this->resetForm();

        if ($this->courseId) {
            $this->course_id = $this->courseId;
        }

        $this->status = 'active';

        $this->showModal = true;
    }

    /**
     * -----------------------------------------------------
     * Edit
     * -----------------------------------------------------
     */
    public function edit($id)
    {
        $assignment = CourseBatch::findOrFail($id);

        $this->editingId = $assignment->id;

        $this->course_id = $assignment->course_id;

        $this->batch_id = $assignment->batch_id;

        $this->trainer_id = $assignment->trainer_id;

        $this->status = $assignment->status;

        $this->showModal = true;
    }

    /**
     * -----------------------------------------------------
     * Save / Update
     * -----------------------------------------------------
     */
    public function save()
    {
        $this->validate();

        $duplicate = CourseBatch::where('course_id', $this->course_id)
            ->where('batch_id', $this->batch_id)
            ->when($this->editingId, function ($q) {
                $q->where('id', '!=', $this->editingId);
            })
            ->exists();

        if ($duplicate) {

            $this->addError(
                'batch_id',
                'This batch is already assigned to this course.'
            );

            return;
        }

        if ($this->editingId) {

            $assignment = CourseBatch::findOrFail($this->editingId);

            $assignment->update([

                'course_id' => $this->course_id,

                'batch_id' => $this->batch_id,

                'trainer_id' => $this->trainer_id,

                'status' => $this->status,

            ]);

            session()->flash(
                'success',
                'Batch assignment updated successfully.'
            );

            $this->dispatch(
                'swal',
                icon: 'success',
                title: 'Updated Successfully!'
            );

        } else {

            CourseBatch::create([

                'course_id' => $this->course_id,

                'batch_id' => $this->batch_id,

                'trainer_id' => $this->trainer_id,

                'status' => $this->status,

            ]);

            session()->flash(
                'success',
                'Batch assigned successfully.'
            );

            $this->dispatch(
                'swal',
                icon: 'success',
                title: 'Batch Assigned Successfully!'
            );
        }

        $this->closeModal();
    }

    /**
     * -----------------------------------------------------
     * Delete
     * -----------------------------------------------------
     */
    public function delete($id)
    {
        $assignment = CourseBatch::findOrFail($id);

        $assignment->delete();

        session()->flash(
            'success',
            'Assignment deleted successfully.'
        );

        $this->dispatch(
            'swal',
            icon: 'success',
            title: 'Deleted Successfully!'
        );
    }

    /**
     * -----------------------------------------------------
     * Close Modal
     * -----------------------------------------------------
     */
    public function closeModal()
    {
        $this->showModal = false;

        $this->resetForm();
    }

    /**
     * -----------------------------------------------------
     * Reset Form
     * -----------------------------------------------------
     */
    public function resetForm()
    {
        $this->editingId = null;

        $this->batch_id = '';

        $this->trainer_id = '';

        $this->status = 'active';

        $this->resetErrorBag();

        $this->resetValidation();

        if (!$this->courseId) {
            $this->course_id = '';
        }
    }

    /**
     * -----------------------------------------------------
     * Course Changed
     * -----------------------------------------------------
     */
    public function updatedCourseId()
    {
        $this->batch_id = '';
    }

    /**
     * -----------------------------------------------------
     * Render
     * -----------------------------------------------------
     */
    public function render()
    {
        return view('livewire.admin.course-batch-assignment', [

            'courses' => $this->courses,

            'batches' => $this->batches,

            'trainers' => $this->trainers,

            'assignments' => $this->assignments,

        ]);
    }
}