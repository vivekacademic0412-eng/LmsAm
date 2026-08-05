<?php

namespace App\Livewire;

use App\Models\DemoTask;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class DemoTaskManager extends Component
{
    use WithPagination;
    use WithFileUploads;

    // ---- Filters / list state ----
    public string $search = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;
    public string $dateFrom = '';
    public string $dateTo = '';

    // ---- Modal state ----
    public bool $showModal = false;
    public bool $isEdit = false;
    public ?int $editingId = null;

    // ---- Form fields ----
    public string $title = '';
    public string $description = '';
    public string $resource_url = '';
    public string $ai_video_url = '';

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $resource_file;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $task_video;

    // Existing file info (edit mode) so we can show current file & only replace if a new one is chosen
    public ?string $existing_resource_file_name = null;
    public ?string $existing_task_video_name = null;

    protected function rules(): array
    {
        return [
            'title'        => 'required|string|min:3|max:150',
            'description'  => 'required|string|min:10',
            'resource_url' => 'nullable|url|max:2048',
            'ai_video_url' => 'nullable|url|max:2048',
            'resource_file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,png,jpg,jpeg',
            'task_video'    => 'nullable|file|max:51200|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/webm',
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required'       => 'Please enter a task title.',
            'title.min'            => 'Title must be at least 3 characters.',
            'description.required' => 'Please write a task description.',
            'description.min'      => 'Description must be at least 10 characters.',
            'resource_url.url'     => 'Resource URL must be a valid URL.',
            'ai_video_url.url'     => 'AI Video URL must be a valid URL.',
            'resource_file.max'    => 'Resource file may not be larger than 10MB.',
            'resource_file.mimes'  => 'Resource file must be a pdf, doc, ppt, xls, zip or image file.',
            'task_video.max'       => 'Task video may not be larger than 50MB.',
            'task_video.mimetypes' => 'Task video must be a valid video file (mp4, mov, avi, webm).',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'dateFrom', 'dateTo']);
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $task = DemoTask::findOrFail($id);

        $this->resetForm();
        $this->editingId = $task->id;
        $this->title = $task->title;
        $this->description = $task->description;
        $this->resource_url = $task->resource_url ?? '';
        $this->ai_video_url = $task->ai_video_url ?? '';
        $this->existing_resource_file_name = $task->resource_file_name;
        $this->existing_task_video_name = $task->task_video_name;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->reset([
            'title',
            'description',
            'resource_url',
            'ai_video_url',
            'resource_file',
            'task_video',
            'editingId',
            'existing_resource_file_name',
            'existing_task_video_name',
        ]);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function save(): void
    {
        $validated = $this->validate();

        $data = [
            'title'        => $validated['title'],
            'description'  => $validated['description'],
            'resource_url' => $validated['resource_url'] ?: null,
            'ai_video_url' => $validated['ai_video_url'] ?: null,
        ];

        if ($this->resource_file) {
            $path = $this->resource_file->store('demo-tasks/resources', 'public');
            $data['resource_file_path'] = $path;
            $data['resource_file_name'] = $this->resource_file->getClientOriginalName();
            $data['resource_file_mime'] = $this->resource_file->getMimeType();
            $data['resource_file_size'] = $this->resource_file->getSize();
        }

        if ($this->task_video) {
            $path = $this->task_video->store('demo-tasks/videos', 'public');
            $data['task_video_path'] = $path;
            $data['task_video_name'] = $this->task_video->getClientOriginalName();
            $data['task_video_mime'] = $this->task_video->getMimeType();
            $data['task_video_size'] = $this->task_video->getSize();
        }

        if ($this->isEdit && $this->editingId) {
            $task = DemoTask::findOrFail($this->editingId);

            // Replace old files on disk if new ones were uploaded
            if ($this->resource_file && $task->resource_file_path) {
                Storage::disk('public')->delete($task->resource_file_path);
            }
            if ($this->task_video && $task->task_video_path) {
                Storage::disk('public')->delete($task->task_video_path);
            }

            $task->update($data);

            $this->dispatch('swal:success', message: 'Task updated successfully.');
        } else {
            $data['created_by'] = Auth::id();
            DemoTask::create($data);

            $this->dispatch('swal:success', message: 'Task created successfully.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->dispatch('swal:confirm-delete', id: $id);
    }

    public function delete(int $id): void
    {
        $task = DemoTask::find($id);

        if (! $task) {
            $this->dispatch('swal:error', message: 'Task not found.');
            return;
        }

        if ($task->resource_file_path) {
            Storage::disk('public')->delete($task->resource_file_path);
        }
        if ($task->task_video_path) {
            Storage::disk('public')->delete($task->task_video_path);
        }

        $task->delete();

        $this->dispatch('swal:success', message: 'Task deleted successfully.');

        // If the current page becomes empty after deletion, step back a page
        if ($this->getTasksProperty()->isEmpty() && $this->getPage() > 1) {
            $this->previousPage();
        }
    }

    public function removeResourceFile(): void
    {
        if ($this->editingId) {
            $task = DemoTask::find($this->editingId);
            if ($task && $task->resource_file_path) {
                Storage::disk('public')->delete($task->resource_file_path);
                $task->update([
                    'resource_file_path' => null,
                    'resource_file_name' => null,
                    'resource_file_mime' => null,
                    'resource_file_size' => null,
                ]);
            }
        }
        $this->existing_resource_file_name = null;
        $this->resource_file = null;
    }

    public function removeTaskVideo(): void
    {
        if ($this->editingId) {
            $task = DemoTask::find($this->editingId);
            if ($task && $task->task_video_path) {
                Storage::disk('public')->delete($task->task_video_path);
                $task->update([
                    'task_video_path' => null,
                    'task_video_name' => null,
                    'task_video_mime' => null,
                    'task_video_size' => null,
                ]);
            }
        }
        $this->existing_task_video_name = null;
        $this->task_video = null;
    }

    public function getTasksProperty()
    {
        return DemoTask::query()
            ->with('creator')
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('title', 'like', '%' . $this->search . '%')
                       ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.demo-task-manager', [
            'tasks' => $this->tasks,
        ]);
    }
}