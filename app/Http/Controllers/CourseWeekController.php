<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSession;
use App\Models\CourseSessionItem;
use App\Models\CourseWeek;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\CloudinaryPrivateMediaService;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CourseWeekController extends Controller
{
    public function storeWeek(Request $request, Course $course): RedirectResponse
    {
        $this->ensureCanManage($request);

        $data = $request->validate([
            'week_number' => [
                'required',
                'integer',
                'min:1',
                'max:260',
                Rule::unique('course_weeks', 'week_number')->where(fn ($query) => $query->where('course_id', $course->id)),
            ],
            'title' => ['required', 'string', 'max:180'],
        ]);

        CourseWeek::create([
            'course_id' => $course->id,
            'week_number' => $data['week_number'],
            'title' => $data['title'],
        ]);

        return back()->with('success', 'Course week created.');
    }

    public function updateWeek(Request $request, CourseWeek $week): RedirectResponse
    {
        $this->ensureCanManage($request);

        $data = $request->validate([
            'week_number' => [
                'required',
                'integer',
                'min:1',
                'max:260',
                Rule::unique('course_weeks', 'week_number')
                    ->where(fn ($query) => $query->where('course_id', $week->course_id))
                    ->ignore($week->id),
            ],
            'title' => ['required', 'string', 'max:180'],
        ]);

        $week->update([
            'week_number' => $data['week_number'],
            'title' => $data['title'],
        ]);

        return back()->with('success', 'Week updated.');
    }

    public function destroyWeek(Request $request, CourseWeek $week): RedirectResponse
    {
        $this->ensureCanManage($request);
        $week->delete();

        return back()->with('success', 'Week deleted.');
    }

    public function storeSession(Request $request, CourseWeek $week): RedirectResponse
    {
        $this->ensureCanManage($request);

        $data = $request->validate([
            'session_number' => [
                'required',
                'integer',
                'min:1',
                'max:200',
                Rule::unique('course_sessions', 'session_number')->where(fn ($query) => $query->where('course_week_id', $week->id)),
            ],
            'title' => ['required', 'string', 'max:180'],
        ]);

        $session = CourseSession::create([
            'course_week_id' => $week->id,
            'session_number' => $data['session_number'],
            'title' => $data['title'],
        ]);

        $defaultItems = [
            ['item_type' => CourseSessionItem::TYPE_INTRO, 'title' => 'Intro PPT / Video', 'resource_type' => 'video_or_ppt'],
            ['item_type' => CourseSessionItem::TYPE_MAIN_VIDEO, 'title' => 'Main Video', 'resource_type' => 'video'],
            ['item_type' => CourseSessionItem::TYPE_TASK, 'title' => 'Task', 'resource_type' => null],
            ['item_type' => CourseSessionItem::TYPE_QUIZ, 'title' => 'Quiz', 'resource_type' => null, 'content' => 'Quiz: Write a short answer based on this session.'],
        ];

        foreach ($defaultItems as $item) {
            $session->items()->create($item);
        }

        return back()->with('success', 'Session created with intro/main video/task/quiz items.');
    }

    public function updateSession(Request $request, CourseSession $session): RedirectResponse
    {
        $this->ensureCanManage($request);

        $data = $request->validate([
            'session_number' => [
                'required',
                'integer',
                'min:1',
                'max:200',
                Rule::unique('course_sessions', 'session_number')
                    ->where(fn ($query) => $query->where('course_week_id', $session->course_week_id))
                    ->ignore($session->id),
            ],
            'title' => ['required', 'string', 'max:180'],
        ]);

        $session->update([
            'session_number' => $data['session_number'],
            'title' => $data['title'],
        ]);

        return back()->with('success', 'Session updated.');
    }

    public function destroySession(Request $request, CourseSession $session): RedirectResponse
    {
        $this->ensureCanManage($request);
        $session->delete();

        return back()->with('success', 'Session deleted.');
    }

    public function updateItem(Request $request, CourseSessionItem $item): RedirectResponse
    {
        $this->ensureCanManage($request);
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');
        @ini_set('max_input_time', '0');

        $baseRules = [
            'title' => ['required', 'string', 'max:180'],
            'resource_type' => ['nullable', 'string', Rule::in(['video', 'ppt', 'video_or_ppt'])],
            'content' => ['nullable', 'string', 'max:2000'],
            'resource_url' => ['nullable', 'url', 'max:500'],
        ];

        $defaultMimes = 'mp4,mov,avi,mkv,pdf,ppt,pptx,doc,docx,xls,xlsx';

        $baseRules['resource_file'] = $item->item_type === CourseSessionItem::TYPE_TASK
            ? ['nullable', 'file', 'max:307200']
            : ['nullable', 'file', 'mimes:'.$defaultMimes, 'max:307200'];

        $data = $request->validate($baseRules);

        $hasFileOrUrl = $request->hasFile('resource_file') || ! empty($data['resource_url']);
        $quizHasContent = $item->item_type === CourseSessionItem::TYPE_QUIZ && ! empty($data['content']);

        if (! $hasFileOrUrl && ! $quizHasContent) {
            throw ValidationException::withMessages([
                'resource_file' => 'Upload a file or provide an external URL.',
                'resource_url' => 'Upload a file or provide an external URL.',
            ]);
        }

        $updateData = [
            'title' => $data['title'],
            'resource_type' => $data['resource_type'] ?? null,
            'content' => $data['content'] ?? null,
            'resource_url' => $data['resource_url'] ?? null,
        ];

        if ($request->hasFile('resource_file')) {
            $cloudinary = app(CloudinaryPrivateMediaService::class);
            if (! $cloudinary->isConfigured()) {
                throw ValidationException::withMessages([
                    'resource_file' => 'Cloudinary is not configured. Add credentials in .env first.',
                ]);
            }

            $oldPublicId = $item->cloudinary_public_id;
            $oldResourceType = $item->cloudinary_resource_type ?: 'raw';
            $oldDeliveryType = $item->cloudinary_delivery_type ?: 'upload';

            $uploaded = $cloudinary->uploadPrivateCourseContent($request->file('resource_file'));

            $updateData['cloudinary_public_id'] = $uploaded['public_id'];
            $updateData['cloudinary_resource_type'] = $uploaded['resource_type'];
            $updateData['cloudinary_format'] = $uploaded['format'];
            $updateData['cloudinary_delivery_type'] = $uploaded['delivery_type'];
            $updateData['resource_url'] = null;

            if ($oldPublicId && $oldPublicId !== $updateData['cloudinary_public_id']) {
                $cloudinary->deleteAsset($oldPublicId, $oldResourceType, $oldDeliveryType);
            }
        } elseif (! empty($updateData['resource_url'])) {
            $updateData['cloudinary_public_id'] = null;
            $updateData['cloudinary_resource_type'] = null;
            $updateData['cloudinary_format'] = null;
            $updateData['cloudinary_delivery_type'] = null;
        }

        $item->update($updateData);

        return back()->with('success', 'Session item updated.');
    }

    private function ensureCanManage(Request $request): void
    {
        abort_unless(
            in_array($request->user()?->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true),
            403
        );
    }

}
