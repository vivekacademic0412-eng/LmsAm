<?php

namespace App\Livewire\Student;

use App\Models\DemoTaskSubmission;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DemoTasks extends Component
{
    public $demoAssignments = [];

    public $answer_text;

    public $submission_file;

    public $assignment;

    protected $listeners = ['do-submit-task' => 'submit'];

    public function mount($demoAssignments,)
    {
      
        $this->demoAssignments = $demoAssignments ?? [];
    }

    public function submit()
    {
        $user = Auth::user();

        abort_unless($user?->role === User::ROLE_DEMO, 403);

        // FIX: assignment null safety
        abort_unless($this->assignment && (int) $this->assignment->user_id === (int) $user->id, 403);

        $this->validate([
            'answer_text' => 'nullable|string|max:4000',
            'submission_file' => 'nullable|file|max:307200',
        ]);

        if (! $this->answer_text && ! $this->submission_file) {
            $this->dispatch('error', message: 'Please provide an answer or upload a file.');

            return;
        }

        $payload = [
            'demo_task_assignment_id' => $this->assignment->id,
            'answer_text' => $this->answer_text,
            'submitted_at' => now(),
        ];

        if ($this->submission_file) {

            $file = $this->submission_file;

            $safeName = preg_replace('/[^a-zA-Z0-9._-]+/', '-', $file->getClientOriginalName());

            $path = $file->storeAs(
                'demo-task-submissions/'.$this->assignment->id,
                uniqid('submission_', true).'-'.$safeName
            );

            $payload['file_path'] = $path;
            $payload['file_name'] = $file->getClientOriginalName();
            $payload['file_mime'] = $file->getClientMimeType();
            $payload['file_size'] = $file->getSize();
        }

        DemoTaskSubmission::create($payload);

        $this->dispatch('success', message: 'Demo task submitted successfully!');

        $this->reset(['answer_text', 'submission_file']);
    }

    public function render()
    {
        return view('livewire.student.demo-tasks');
    }
}
