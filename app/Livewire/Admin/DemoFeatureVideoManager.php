<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\DemoFeatureVideo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class DemoFeatureVideoManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $videoId;

    public $position;
    public $title;
    public $description;
    public $video_file;
    public $category_id;
    public $search = '';
    public $filterPosition = '';

    public $isEdit = false;

    /** Controls the upload/edit modal — the form no longer sits inline on the page. */
    public bool $showModal = false;

    /** Rows per page. */
    protected int $perPage = 8;

    protected $paginationTheme = 'bootstrap';

    protected $messages = [
        'category_id.required' => 'Please select a course.',
        'category_id.exists'   => 'Please select a valid course.',
        'category_id.unique'   => 'This course already has a feature video. Edit the existing one instead of adding a new one.',

        'position.required' => 'Position is required.',
        'position.integer'  => 'Position must be a number.',
        'position.min'      => 'Position must be at least 1.',
        'position.unique'   => 'This position is already used by another video. Choose a different one.',

        'video_file.required' => 'Please upload a video.',
        'video_file.mimes'    => 'Allowed formats: MP4, MOV, AVI, MKV, WEBM.',
        'video_file.max'      => 'Maximum upload size exceeded.',

        'title.max'       => 'Title cannot exceed 180 characters.',
        'description.max' => 'Description cannot exceed 2000 characters.',
    ];

    protected function rules()
    {
        return [
            'category_id' => [
                'required',
                'exists:course_categories,id',
                // One feature video per subject/category.
                Rule::unique((new DemoFeatureVideo())->getTable(), 'category_id')
                    ->ignore($this->videoId),
            ],
            'position' => [
                'required',
                'integer',
                'min:1',
                Rule::unique((new DemoFeatureVideo())->getTable(), 'position')
                    ->ignore($this->videoId),
            ],
            'title' => [
                'nullable',
                'string',
                'max:180',
            ],
            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'video_file' => $this->isEdit
                ? 'nullable|mimes:mp4,mov,avi,mkv,webm|max:2048000'
                : 'required|mimes:mp4,mov,avi,mkv,webm|max:2048000',
        ];
    }

    public function updated($property)
    {
        $this->validateOnly($property);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterPosition()
    {
        $this->resetPage();
    }

    /* ── Modal control ── */

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->showModal = false;
    }

    public function save()
    {
        $this->validate();

        try {
            $fileName = $this->video_file->getClientOriginalName();
            $fileMime = $this->video_file->getMimeType();
            $fileSize = $this->video_file->getSize();

            $safeName = preg_replace('/[^a-zA-Z0-9._-]+/', '-', $fileName);

            $path = $this->video_file->storeAs(
                'demo-feature-video',
                uniqid('feature_', true) . '-' . $safeName,
                'public'
            );

            DemoFeatureVideo::create([
                'category_id' => $this->category_id,
                'position'    => $this->position,
                'title'       => $this->title,
                'description' => $this->description,
                'file_path'   => $path,
                'file_name'   => $fileName,
                'file_mime'   => $fileMime,
                'file_size'   => $fileSize,
                'uploaded_by' => auth()->id(),
                'status'      => 1,
            ]);

            $this->resetForm();
            $this->showModal = false;
            $this->resetPage();

            $this->dispatch('video-created');

        } catch (\Throwable $e) {
            Log::error('Feature video upload failed', ['message' => $e->getMessage()]);
            $this->dispatch('video-error', message: 'Could not save the video. Please try again.');
        }
    }

    public function edit($id)
    {
        $video = DemoFeatureVideo::findOrFail($id);

        $this->category_id = $video->category_id;
        $this->videoId     = $video->id;
        $this->position    = $video->position;
        $this->title       = $video->title;
        $this->description = $video->description;

        $this->isEdit    = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();

        try {
            $video = DemoFeatureVideo::findOrFail($this->videoId);

            if ($this->video_file) {
                if ($video->file_path && Storage::disk('public')->exists($video->file_path)) {
                    Storage::disk('public')->delete($video->file_path);
                }

                $safeName = preg_replace(
                    '/[^a-zA-Z0-9._-]+/',
                    '-',
                    $this->video_file->getClientOriginalName()
                );

                $path = $this->video_file->storeAs(
                    'demo-feature-video',
                    uniqid('feature_', true) . '-' . $safeName,
                    'public'
                );

                $video->update([
                    'file_path' => $path,
                    'file_name' => $this->video_file->getClientOriginalName(),
                    'file_mime' => $this->video_file->getMimeType(),
                    'file_size' => $this->video_file->getSize(),
                ]);
            }

            $video->update([
                'category_id' => $this->category_id,
                'position'    => $this->position,
                'title'       => $this->title,
                'description' => $this->description,
            ]);

            $this->resetForm();
            $this->showModal = false;

            $this->dispatch('video-updated');

        } catch (\Throwable $e) {
            Log::error('Feature video update failed', [
                'video_id' => $this->videoId,
                'message'  => $e->getMessage(),
            ]);
            $this->dispatch('video-error', message: 'Could not update the video. Please try again.');
        }
    }

    public function delete($id)
    {
        try {
            $video = DemoFeatureVideo::findOrFail($id);

            if ($video->file_path && Storage::disk('public')->exists($video->file_path)) {
                Storage::disk('public')->delete($video->file_path);
            }

            $video->delete();

            $this->resetPage();

            $this->dispatch('video-deleted');

        } catch (\Throwable $e) {
            Log::error('Feature video delete failed', [
                'video_id' => $id,
                'message'  => $e->getMessage(),
            ]);
            $this->dispatch('video-error', message: 'Could not delete the video. Please try again.');
        }
    }

    public function resetForm()
    {
        $this->reset([
            'category_id',
            'videoId',
            'position',
            'title',
            'description',
            'video_file',
        ]);

        $this->resetValidation();

        $this->isEdit = false;
    }

    public function render()
    {
        $query = DemoFeatureVideo::query();

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if (! empty($this->filterPosition)) {
            $query->where('position', $this->filterPosition);
        }

        $videos = $query->with('category')->orderBy('position')->latest()->paginate($this->perPage);

        $featured = DemoFeatureVideo::orderBy('position')->first();

        $nextPosition = (DemoFeatureVideo::max('position') ?? 0) + 1;

        $totalVideos = DemoFeatureVideo::count();

        $categories = CourseCategory::orderBy('name')->get();

        // One video per subject/category — categories that already have a
        // video are shown disabled in the dropdown, except the one
        // currently being edited (so its own category stays selectable).
        $usedCategoryIds = DemoFeatureVideo::when(
            $this->videoId,
            fn ($q) => $q->where('id', '!=', $this->videoId)
        )->pluck('category_id')->all();

        return view('livewire.admin.demo-feature-video-manager', [
            'videos'          => $videos,
            'featured'        => $featured,
            'nextPosition'    => $nextPosition,
            'totalVideos'     => $totalVideos,
            'categories'      => $categories,
            'usedCategoryIds' => $usedCategoryIds,
        ]);
    }
}