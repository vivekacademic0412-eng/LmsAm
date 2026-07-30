<?php

namespace App\Livewire\Admin;

use App\Models\Batch;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BatchManager extends Component
{
    // Optional route-bound course, only used to prefill the course filter on load.
    // Everything else (list, create, edit) works with or without it.
    public ?Course $course = null;

    // ── Filters (list page) ──
    public $filterCourseId = '';
    public $filterStatus   = '';
    public $filterMode     = '';
    public $search         = '';

    // ── Form state (Add/Edit modal) ──
    public ?int $editingId = null;
    public $course_id     = '';   // required in the FORM itself — this is what actually gets saved.
    public $trainer_id    = '';
    public $batch_code    = '';
    public $mode          = 'online';
    public $start_date    = '';
    public $start_time    = '';
    public $zero_day_date = '';
    public $max_weeks     = '';
    public $max_seats     = '';
    public $status        = 'upcoming';

    public bool $showForm            = false;
    public ?int $confirmingDeleteId  = null;

    public function mount(?Course $course = null)
    {
        $this->course         = $course;
        $this->filterCourseId = $course?->id ?? '';
    }

    protected function rules(): array
    {
        return [
            // This is the fix for the null-course_id bug: course is a required, validated
            // field on the form, saved straight onto the Batch row — never inferred from
            // a relation on a maybe-empty $this->course.
            'course_id'     => ['required', 'exists:courses,id'],
            'trainer_id'    => ['required', 'exists:users,id'],
            'batch_code'    => [
                'required', 'string', 'max:50',
                'unique:batches,batch_code,' . ($this->editingId ?? 'NULL') . ',id',
            ],
            'mode'          => ['required', 'in:online,offline,hybrid'],
            'start_date'    => ['required', 'date'],
            'start_time'    => ['required', 'date_format:H:i'],
            'zero_day_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'max_weeks'     => ['required', 'integer', 'min:1', 'max:104'],
            'max_seats'     => ['required', 'integer', 'min:1', 'max:1000'],
            'status'        => ['required', 'in:upcoming,active,completed,cancelled'],
        ];
    }

    protected function messages(): array
    {
        return [
            'course_id.required'           => 'Pick which course this batch belongs to.',
            'batch_code.unique'            => 'That batch code is already used by another batch.',
            'start_time.date_format'       => 'Enter a start time, e.g. 09:10.',
            'zero_day_date.after_or_equal' => "Zero day can't be before the start date.",
        ];
    }

    public function getCoursesProperty()
    {
        return Course::orderBy('title')->get(['id', 'title']);
    }

    // ASSUMPTION: users have a `role` column with a 'trainer' value.
    public function getTrainersProperty()
    {
        return User::where('role', 'trainer')->orderBy('name')->get(['id', 'name']);
    }

    public function getBatchesProperty()
    {
        return Batch::query()
            ->withCount(['students as active_students_count' => fn ($q) => $q->where('status', '!=', 'cancelled')])
            ->with(['trainer:id,name', 'course:id,title'])
            ->when($this->filterCourseId, fn ($q) => $q->where('course_id', $this->filterCourseId))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterMode, fn ($q) => $q->where('mode', $this->filterMode))
            ->when($this->search, fn ($q) => $q->where('batch_code', 'like', '%' . $this->search . '%'))
            ->orderByDesc('start_date')
            ->get();
    }

    public function clearFilters()
    {
        $this->reset(['filterCourseId', 'filterStatus', 'filterMode', 'search']);
    }

    public function create()
    {
        $this->resetForm();
        // Convenience defaults, both still fully editable/validated before saving.
        $this->course_id = $this->filterCourseId ?: '';
        $this->max_seats = 50;
        $this->showForm  = true;
    }

    public function edit($batchId)
    {
        $batch = Batch::findOrFail($batchId);

        $this->editingId     = $batch->id;
        $this->course_id     = $batch->course_id;
        $this->trainer_id    = $batch->trainer_id;
        $this->batch_code    = $batch->batch_code;
        $this->mode          = $batch->mode;
        $this->start_date    = optional($batch->start_date)->format('Y-m-d');
        $this->start_time    = $batch->start_time ? substr($batch->start_time, 0, 5) : '';
        $this->zero_day_date = optional($batch->zero_day_date)->format('Y-m-d');
        $this->max_weeks     = $batch->max_weeks;
        $this->max_seats     = $batch->max_seats;
        $this->status        = $batch->status;

        $this->showForm = true;
    }

    public function save()
    {
        $data = $this->validate();

        if ($this->editingId) {
            $batch = Batch::findOrFail($this->editingId);

            $taken = $batch->students()->where('status', '!=', 'cancelled')->count();
            if ((int) $data['max_seats'] < $taken) {
                $this->addError('max_seats', "Can't drop capacity below the {$taken} seat(s) already taken.");
                return;
            }

            $batch->update($data);
            $message = "Batch \"{$batch->batch_code}\" updated.";
        } else {
            $batch = Batch::create($data + ['created_by' => auth()->id()]);
            $message = "Batch \"{$batch->batch_code}\" created.";
        }

        $this->showForm = false;
        $this->resetForm();
        $this->dispatch('batch-saved', message: $message);
    }

    public function confirmDelete($batchId)
    {
        $this->confirmingDeleteId = $batchId;
    }

    public function delete()
    {
        $batch = Batch::findOrFail($this->confirmingDeleteId);

        $activeStudents = $batch->students()->where('status', '!=', 'cancelled')->count();
        if ($activeStudents > 0) {
            $this->addError('delete', "Can't delete — {$activeStudents} student(s) are still active in this batch.");
            $this->confirmingDeleteId = null;
            return;
        }

        $code = $batch->batch_code;

        DB::transaction(function () use ($batch) {
            $batch->students()->delete();
            $batch->delete();
        });

        $this->confirmingDeleteId = null;
        $this->dispatch('batch-deleted', message: "Batch \"{$code}\" deleted.");
    }

    public function cancelDelete()
    {
        $this->confirmingDeleteId = null;
    }

    public function closeForm()
    {
        $this->showForm = false;
        $this->resetForm();
    }

    protected function resetForm()
    {
        $this->reset([
            'editingId', 'course_id', 'trainer_id', 'batch_code',
            'start_date', 'start_time', 'zero_day_date', 'max_weeks', 'max_seats',
        ]);
        $this->mode   = 'online';
        $this->status = 'upcoming';
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.batch-manager', [
            'courses'  => $this->courses,
            'trainers' => $this->trainers,
            'batches'  => $this->batches,
        ]);
    }
}