<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseDay;
use App\Models\CourseDayItem;
use App\Services\CloudinaryPrivateMediaService;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CourseDayController extends Controller
{
    public function store(Request $request, Course $course): RedirectResponse
    {
        $this->ensureCanManage($request);

        $data = $request->validate([
            'day_number' => [
                'required',
                'integer',
                'min:1',
                'max:365',
                Rule::unique('course_days', 'day_number')->where(fn ($query) => $query->where('course_id', $course->id)),
            ],
            'title' => ['required', 'string', 'max:180'],
        ]);

        $day = CourseDay::create([
            'course_id' => $course->id,
            'day_number' => $data['day_number'],
            'title' => $data['title'],
        ]);

        $defaultItems = [
            ['item_type' => CourseDayItem::TYPE_INTRO, 'title' => 'Intro PPT / Video', 'resource_type' => 'video_or_ppt'],
            ['item_type' => CourseDayItem::TYPE_MAIN_VIDEO, 'title' => 'Main Video', 'resource_type' => 'video'],
            ['item_type' => CourseDayItem::TYPE_TASK, 'title' => 'Task', 'resource_type' => null],
            ['item_type' => CourseDayItem::TYPE_QUIZ, 'title' => 'Quiz', 'resource_type' => null],
        ];

        foreach ($defaultItems as $item) {
            $day->items()->create($item);
        }

        return back()->with('success', 'Course day and default 4 items created.');
    }

    public function updateItem(Request $request, CourseDayItem $item): RedirectResponse
    {
        $this->ensureCanManage($request);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'resource_type' => ['nullable', 'string', Rule::in(['video', 'ppt', 'video_or_ppt'])],
            'content' => ['nullable', 'string', 'max:2000'],
            'resource_url' => ['nullable', 'url', 'max:500'],
            'resource_file' => $item->item_type === CourseDayItem::TYPE_TASK
                ? ['nullable', 'file', 'max:307200']
                : ['nullable', 'file', 'mimes:mp4,mov,avi,mkv,pdf,ppt,pptx,doc,docx,xls,xlsx', 'max:307200'],
        ]);

        $updateData = [
            'title' => $data['title'],
            'resource_type' => $data['resource_type'] ?? null,
            'content' => $data['content'] ?? null,
            'resource_url' => $data['resource_url'] ?? null,
        ];

        $requiresSecureUpload = in_array($updateData['resource_type'], ['video', 'ppt', 'video_or_ppt'], true);
        if ($requiresSecureUpload && ! $request->hasFile('resource_file') && ! empty($updateData['resource_url'])) {
            throw ValidationException::withMessages([
                'resource_url' => 'External URL is not allowed for video/PPT. Upload a secure file instead.',
            ]);
        }

        if ($request->hasFile('resource_file')) {
            $cloudinary = app(CloudinaryPrivateMediaService::class);
            if (! $cloudinary->isConfigured()) {
                throw ValidationException::withMessages([
                    'resource_file' => 'Cloudinary is not configured. Add Cloudinary credentials in .env first.',
                ]);
            }

            $uploaded = $cloudinary->uploadPrivateCourseContent($request->file('resource_file'));

            $updateData['resource_url'] = null;
            $updateData['cloudinary_public_id'] = $uploaded['public_id'];
            $updateData['cloudinary_resource_type'] = $uploaded['resource_type'];
            $updateData['cloudinary_format'] = $uploaded['format'];
            $updateData['cloudinary_delivery_type'] = $uploaded['delivery_type'];
        }

        $item->update($updateData);

        return back()->with('success', 'Day item updated.');
    }

    private function ensureCanManage(Request $request): void
    {
        abort_unless(
            in_array($request->user()?->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true),
            403
        );
    }
}
