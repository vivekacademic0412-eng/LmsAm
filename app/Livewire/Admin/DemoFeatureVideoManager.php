<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use App\Models\DemoFeatureVideo;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class DemoFeatureVideoManager extends Component
{
    use WithFileUploads;

    public $videoId;

    public $position;
    public $title;
    public $description;
    public $video_file;
    public $course;
    public $search = '';
    public $filterPosition = '';

    public $isEdit = false;

    protected $messages = [
        'position.required' => 'Position is required.',
        'position.integer' => 'Position must be a number.',

        'video_file.required' => 'Please upload a video.',
        'video_file.mimes' => 'Allowed formats: MP4, MOV, AVI, MKV, WEBM.',
        'video_file.max' => 'Maximum upload size exceeded.',

        'title.max' => 'Title cannot exceed 180 characters.',
        'description.max' => 'Description cannot exceed 2000 characters.',
    ];

    protected function rules()
    {
        return [
            'course' => [
                'required',
                'exists:courses,id'
            ],
            'position' => [
                'required',
                'integer',
                'min:1'
            ],

            'title' => [
                'nullable',
                'string',
                'max:180'
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000'
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

    public function save()
    {

        $this->validate();

        $fileName = null;
        $fileMime = null;
        $fileSize = null;
        $path = null;

        if ($this->video_file) {

            $fileName = $this->video_file->getClientOriginalName();
            $fileMime = $this->video_file->getMimeType();
            $fileSize = $this->video_file->getSize();

            $safeName = preg_replace(
                '/[^a-zA-Z0-9._-]+/',
                '-',
                $fileName
            );

            $path = $this->video_file->storeAs(
                'demo-feature-video',
                uniqid('feature_', true) . '-' . $safeName,
                'public'
            );
        }

        DemoFeatureVideo::create([
            'couses_id' => $this->course,
            'position' => $this->position,
            'title' => $this->title,
            'description' => $this->description,
            'file_path' => $path,
            'file_name' => $fileName,
            'file_mime' => $fileMime,
            'file_size' => $fileSize,
            'uploaded_by' => auth()->id(),
            'status' => 1,

        ]);

        $this->resetForm();

        $this->dispatch('video-created');
    }

    public function edit($id)
    {
        $video = DemoFeatureVideo::findOrFail($id);
        $this->course = $video->course_id;
        $this->videoId = $video->id;

        $this->position = $video->position;

        $this->title = $video->title;

        $this->description = $video->description;

        $this->isEdit = true;
    }

    public function update()
    {
        $this->validate();

        $video = DemoFeatureVideo::findOrFail($this->videoId);

        if ($this->video_file) {

            if (
                $video->file_path &&
                Storage::disk('public')->exists($video->file_path)
            ) {
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
            'course_id' => $this->course,
            'position' => $this->position,

            'title' => $this->title,

            'description' => $this->description,

        ]);

        $this->resetForm();

        $this->dispatch('video-updated');
    }

    public function delete($id)
    {

        $video = DemoFeatureVideo::findOrFail($id);

        if (
            $video->file_path &&
            Storage::disk('public')->exists($video->file_path)
        ) {
            Storage::disk('public')->delete($video->file_path);
        }

        $video->delete();

        $this->dispatch('video-deleted');
    }

    public function resetForm()
    {
        $this->reset([
            'course',
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

        if (!empty($this->search)) {

            $query->where(function ($q) {

                $q->where(
                    'title',
                    'like',
                    '%' . $this->search . '%'
                )
                    ->orWhere(
                        'description',
                        'like',
                        '%' . $this->search . '%'
                    );
            });
        }

        if (!empty($this->filterPosition)) {

            $query->where(
                'position',
                $this->filterPosition
            );
        }

        $videos = $query
            ->orderBy('position')
            ->latest()
            ->get();

        $featured = DemoFeatureVideo::orderBy('position')
            ->first();

        $nextPosition =
            (DemoFeatureVideo::max('position') ?? 0) + 1;
        $courses = Course::orderBy('title')
            ->get();

        return view(
            'livewire.admin.demo-feature-video-manager',
            [
                'videos' => $videos,
                'featured' => $featured,
                'nextPosition' => $nextPosition,
                'courses' => $courses,
            ]
        );
    }
}
