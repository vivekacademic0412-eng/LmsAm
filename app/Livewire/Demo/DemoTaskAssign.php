<?php

namespace App\Livewire\Demo;

use App\Models\DemoTask;
use App\Models\DemoTaskAssignment;
use App\Models\DemoTaskSubmission;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class DemoTaskAssign extends Component
{
    use WithPagination;

    // Assign form
    public $demo_task_id = '';
    public $user_id = '';

    // Edit form
    public ?int $editingAssignmentId = null;
    public $edit_demo_task_id = '';
    public $edit_user_id = '';

    // Delete
    public ?int $deletingAssignmentId = null;

    protected string $paginationTheme = 'tailwind';

    public function mount()
    {
        abort_unless(Auth::user()?->role === User::ROLE_ADMIN, 403);
    }

    protected function rules(): array
    {
        return [
            'demo_task_id' => ['required', 'integer', 'exists:demo_tasks,id'],
            'user_id'      => ['required', 'integer', Rule::exists('users', 'id')->where('role', User::ROLE_STUDENT)],
        ];
    }

    protected function editRules(): array
    {
        return [
            'edit_demo_task_id' => ['required', 'integer', 'exists:demo_tasks,id'],
            'edit_user_id'      => ['required', 'integer', Rule::exists('users', 'id')->where('role', User::ROLE_STUDENT)],
        ];
    }

    protected function messages(): array
    {
        return [
            'demo_task_id.required' => 'Please select a task.',
            'demo_task_id.exists'   => 'Selected task no longer exists.',
            'user_id.required'      => 'Please select a demo user.',
            'user_id.exists'        => 'Selected user is not a valid demo user.',

            'edit_demo_task_id.required' => 'Please select a task.',
            'edit_demo_task_id.exists'   => 'Selected task no longer exists.',
            'edit_user_id.required'      => 'Please select a demo user.',
            'edit_user_id.exists'        => 'Selected user is not a valid demo user.',
        ];
    }

    public function assign()
    {
        $data = $this->validate($this->rules());

        DemoTaskAssignment::updateOrCreate(
            [
                'demo_task_id' => $data['demo_task_id'],
                'user_id'      => $data['user_id'],
            ],
            [
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
            ]
        );

        $this->reset(['demo_task_id', 'user_id']);

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Task Assigned!',
            'text'  => 'The demo task has been assigned successfully.',
        ]);
    }

    public function openEdit(int $assignmentId)
    {
        $assignment = DemoTaskAssignment::findOrFail($assignmentId);

        $this->editingAssignmentId = $assignment->id;
        $this->edit_demo_task_id   = $assignment->demo_task_id;
        $this->edit_user_id        = $assignment->user_id;

        $this->dispatch('open-modal', id: "modal-demo-assignment-edit-{$assignment->id}");
    }

    public function updateAssignment()
    {
        $data = $this->validate($this->editRules());

        $assignment = DemoTaskAssignment::findOrFail($this->editingAssignmentId);

        $assignment->update([
            'demo_task_id' => $data['edit_demo_task_id'],
            'user_id'      => $data['edit_user_id'],
        ]);

        $this->dispatch('close-modal', id: "modal-demo-assignment-edit-{$assignment->id}");

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Assignment Updated!',
            'text'  => 'The assignment has been updated successfully.',
        ]);
    }

    public function confirmDelete(int $assignmentId)
    {
        $this->deletingAssignmentId = $assignmentId;
    }

    public function destroyAssignment(int $assignmentId)
    {
        $assignment = DemoTaskAssignment::findOrFail($assignmentId);
        $assignment->delete();

        $this->dispatch('close-modal', id: "modal-demo-assignment-delete-{$assignmentId}");

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Assignment Deleted',
            'text'  => 'The assignment and its submission (if any) were removed.',
        ]);
    }

    public function render()
    {
        $tasks = DemoTask::withCount('assignments')->latest('id')->get();

        $demoUsers = User::where('role', User::ROLE_STUDENT)->orderBy('name')->get();

        $assignments = DemoTaskAssignment::with(['demoTask', 'user', 'assigner'])
            ->latest('id')
            ->paginate(10);

        $latestSubmissions = DemoTaskSubmission::query()
            ->whereIn('demo_task_assignment_id', $assignments->pluck('id'))
            ->latest('submitted_at')
            ->get()
            ->groupBy('demo_task_assignment_id')
            ->map->first();

        return view('livewire.demo.demo-task-assign', [
            'tasks'             => $tasks,
            'demoUsers'         => $demoUsers,
            'assignments'       => $assignments,
            'latestSubmissions' => $latestSubmissions,
        ]);
    }
}