<?php

namespace App\Http\Controllers;

use App\Models\DemoTask;
use App\Models\DemoTaskAssignment;
use App\Models\DemoTaskSubmission;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DemoTaskController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);
        return redirect()->route('demo-tasks.create-page');
    }

    public function createPage(Request $request): View
    {
        $this->authorizeAdmin($request);

        $tasks = DemoTask::withCount('assignments')->latest('id')->paginate(8);

        return view('demo.tasks.create', [
            'tasks' => $tasks,
        ]);
    }

    public function assignPage(Request $request): View
    {
        $this->authorizeAdmin($request);

        $demoUsers = User::where('role', User::ROLE_DEMO)->orderBy('name')->get();

        $tasks = DemoTask::withCount('assignments')->latest('id')->get();

        $assignments = DemoTaskAssignment::with(['demoTask', 'user', 'assigner'])
            ->latest('id')
            ->paginate(10, ['*'], 'assignments_page');

        $latestSubmissions = DemoTaskSubmission::query()
            ->whereIn('demo_task_assignment_id', $assignments->pluck('id'))
            ->latest('submitted_at')
            ->get()
            ->groupBy('demo_task_assignment_id')
            ->map->first();

        return view('demo.tasks.assign', [
            'tasks' => $tasks,
            'demoUsers' => $demoUsers,
            'assignments' => $assignments,
            'latestSubmissions' => $latestSubmissions,
        ]);
    }

    public function submissionsPage(Request $request): View
    {
        $this->authorizeAdmin($request);

        $selectedUser = null;
        $selectedUserId = (int) $request->query('user_id');
        $submissionsQuery = DemoTaskSubmission::with(['assignment.demoTask', 'assignment.user', 'assignment.assigner'])
            ->latest('submitted_at');

        if ($selectedUserId > 0) {
            $selectedUser = User::where('role', User::ROLE_DEMO)->find($selectedUserId);

            if ($selectedUser) {
                $submissionsQuery->whereHas('assignment', function ($query) use ($selectedUserId): void {
                    $query->where('user_id', $selectedUserId);
                });
            }
        }

        $submissions = $submissionsQuery->paginate(12)->withQueryString();

        return view('demo.tasks.submissions', [
            'submissions' => $submissions,
            'selectedUser' => $selectedUser,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:2000'],
            'resource_url' => ['nullable', 'url', 'max:255'],
            'resource_file' => ['nullable', 'file', 'max:307200'],
            'task_video' => $this->taskVideoRules(),
            'ai_video_url' => ['nullable', 'url', 'max:255'],
        ]);

        $payload = [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'resource_url' => $data['resource_url'] ?? null,
            'ai_video_url' => $data['ai_video_url'] ?? null,
            'created_by' => $request->user()->id,
        ];

        if ($request->hasFile('resource_file')) {
            $uploaded = $this->storeResourceFile($request->file('resource_file'));
            $payload['resource_url'] = null;
            $payload['resource_file_path'] = $uploaded['path'];
            $payload['resource_file_name'] = $uploaded['name'];
            $payload['resource_file_mime'] = $uploaded['mime'];
            $payload['resource_file_size'] = $uploaded['size'];
        }

        if ($this->supportsTaskVideo() && $request->hasFile('task_video')) {
            $uploadedVideo = $this->storeTaskVideo($request->file('task_video'));
            $payload['task_video_path'] = $uploadedVideo['path'];
            $payload['task_video_name'] = $uploadedVideo['name'];
            $payload['task_video_mime'] = $uploadedVideo['mime'];
            $payload['task_video_size'] = $uploadedVideo['size'];
        }

        DemoTask::create($payload);

        return back()->with('success', 'Demo task created.');
    }

    public function assign(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $data = $request->validate([
            'demo_task_id' => ['required', 'integer', 'exists:demo_tasks,id'],
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')->where('role', User::ROLE_DEMO)],
        ]);

        $demoTask = DemoTask::findOrFail($data['demo_task_id']);

        DemoTaskAssignment::updateOrCreate(
            [
                'demo_task_id' => $demoTask->id,
                'user_id' => $data['user_id'],
            ],
            [
                'assigned_by' => $request->user()->id,
                'assigned_at' => now(),
            ]
        );

        return back()->with('success', 'Demo task assigned.');
    }

    public function updateAssignment(Request $request, DemoTaskAssignment $assignment): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $data = $request->validate([
            'demo_task_id' => ['required', 'integer', 'exists:demo_tasks,id'],
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')->where('role', User::ROLE_DEMO)],
        ]);

        $duplicateAssignmentExists = DemoTaskAssignment::query()
            ->where('demo_task_id', $data['demo_task_id'])
            ->where('user_id', $data['user_id'])
            ->whereKeyNot($assignment->id)
            ->exists();

        if ($duplicateAssignmentExists) {
            return back()->withErrors([
                'user_id' => 'This demo user is already assigned to the selected demo task.',
            ]);
        }

        $assignment->update([
            'demo_task_id' => $data['demo_task_id'],
            'user_id' => $data['user_id'],
            'assigned_by' => $request->user()->id,
            'assigned_at' => now(),
        ]);

        return back()->with('success', 'Demo task assignment updated.');
    }

    public function destroyAssignment(Request $request, DemoTaskAssignment $assignment): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $assignment->delete();

        return back()->with('success', 'Demo task assignment deleted.');
    }

    public function update(Request $request, DemoTask $demoTask): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:2000'],
            'resource_url' => ['nullable', 'url', 'max:255'],
            'resource_file' => ['nullable', 'file', 'max:307200'],
            'task_video' => $this->taskVideoRules(),
            'ai_video_url' => ['nullable', 'url', 'max:255'],
        ]);

        $updateData = [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'resource_url' => $data['resource_url'] ?? null,
            'ai_video_url' => $data['ai_video_url'] ?? null,
        ];

        if ($request->hasFile('resource_file')) {
            $uploaded = $this->storeResourceFile($request->file('resource_file'));

            if ($demoTask->resource_file_path && Storage::disk('local')->exists($demoTask->resource_file_path)) {
                Storage::disk('local')->delete($demoTask->resource_file_path);
            }

            $updateData['resource_url'] = null;
            $updateData['resource_file_path'] = $uploaded['path'];
            $updateData['resource_file_name'] = $uploaded['name'];
            $updateData['resource_file_mime'] = $uploaded['mime'];
            $updateData['resource_file_size'] = $uploaded['size'];
        }

        if ($this->supportsTaskVideo() && $request->hasFile('task_video')) {
            $uploadedVideo = $this->storeTaskVideo($request->file('task_video'));

            if ($demoTask->task_video_path && Storage::disk('local')->exists($demoTask->task_video_path)) {
                Storage::disk('local')->delete($demoTask->task_video_path);
            }

            $updateData['task_video_path'] = $uploadedVideo['path'];
            $updateData['task_video_name'] = $uploadedVideo['name'];
            $updateData['task_video_mime'] = $uploadedVideo['mime'];
            $updateData['task_video_size'] = $uploadedVideo['size'];
        }

        $demoTask->update($updateData);

        return back()->with('success', 'Demo task updated.');
    }

    public function destroy(Request $request, DemoTask $demoTask): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $demoTask->load('assignments.submissions');
        foreach ($demoTask->assignments as $assignment) {
            foreach ($assignment->submissions as $submission) {
                if ($submission->file_path && Storage::disk('local')->exists($submission->file_path)) {
                    Storage::disk('local')->delete($submission->file_path);
                }
            }
        }

        if ($demoTask->resource_file_path && Storage::disk('local')->exists($demoTask->resource_file_path)) {
            Storage::disk('local')->delete($demoTask->resource_file_path);
        }

        if ($demoTask->task_video_path && Storage::disk('local')->exists($demoTask->task_video_path)) {
            Storage::disk('local')->delete($demoTask->task_video_path);
        }

        $demoTask->delete();

        return back()->with('success', 'Demo task deleted.');
    }

    public function downloadResource(Request $request, DemoTask $demoTask)
    {
        $user = $request->user();
        abort_unless($user, 403);

        abort_unless(
            in_array($user->role, [User::ROLE_DEMO, User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true),
            403
        );

        if (! $demoTask->resource_file_path || ! Storage::disk('local')->exists($demoTask->resource_file_path)) {
            abort(404, 'Demo task resource not found.');
        }

        $filename = trim((string) $demoTask->resource_file_name);
        if ($filename === '') {
            $filename = basename($demoTask->resource_file_path) ?: 'demo-task-resource';
        }

        return Storage::disk('local')->download($demoTask->resource_file_path, $filename);
    }

    public function showVideo(Request $request, DemoTask $demoTask)
    {
        $user = $request->user();
        abort_unless($user, 403);

        abort_unless(
            in_array($user->role, [User::ROLE_DEMO, User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true),
            403
        );

        abort_unless($this->supportsTaskVideo(), 404);

        if (! $demoTask->task_video_path || ! Storage::disk('local')->exists($demoTask->task_video_path)) {
            abort(404, 'Demo task video not found.');
        }

        $path = Storage::disk('local')->path($demoTask->task_video_path);
        $filename = $demoTask->task_video_name ?: basename($demoTask->task_video_path);
        $mime = $demoTask->task_video_mime ?: 'video/mp4';

        return response()->file($path, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    public function submit(Request $request, DemoTaskAssignment $assignment): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user?->role === User::ROLE_DEMO, 403);
        abort_unless((int) $assignment->user_id === (int) $user->id, 403);

        $data = $request->validate([
            'answer_text' => ['nullable', 'string', 'max:4000'],
            'submission_file' => ['nullable', 'file', 'max:307200'],
        ]);

        if (empty($data['answer_text']) && ! $request->hasFile('submission_file')) {
            return back()->withErrors(['answer_text' => 'Please provide an answer or upload a file.']);
        }

        $payload = [
            'demo_task_assignment_id' => $assignment->id,
            'answer_text' => $data['answer_text'] ?? null,
            'submitted_at' => now(),
        ];

        if ($request->hasFile('submission_file')) {
            $file = $request->file('submission_file');
            $safeName = preg_replace('/[^a-zA-Z0-9._-]+/', '-', $file->getClientOriginalName()) ?: 'submission';
            $path = $file->storeAs(
                'demo-task-submissions/'.$assignment->id,
                uniqid('submission_', true).'-'.$safeName
            );

            $payload['file_path'] = $path;
            $payload['file_name'] = $file->getClientOriginalName();
            $payload['file_mime'] = $file->getClientMimeType();
            $payload['file_size'] = $file->getSize();
        }

        DemoTaskSubmission::create($payload);

       return back()->with('success', 'Demo task submitted.');
    }

    public function download(Request $request, DemoTaskSubmission $submission)
    {
        $user = $request->user();
        abort_unless($user, 403);

        $assignment = $submission->assignment;
        if ($user->role === User::ROLE_DEMO) {
            abort_unless((int) $assignment->user_id === (int) $user->id, 403);
        } else {
            $this->authorizeAdmin($request);
        }

        if (! $submission->file_path || ! Storage::disk('local')->exists($submission->file_path)) {
            abort(404, 'Submission file not found.');
        }

        $filename = trim((string) $submission->file_name);
        if ($filename === '') {
            $filename = basename($submission->file_path) ?: 'submission';
        }

        $filename = $this->ensureExtension($filename, $submission->file_mime, $submission->file_path);

        return Storage::disk('local')->download($submission->file_path, $filename);
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(
            in_array($request->user()?->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true),
            403
        );
    }

    private function ensureExtension(string $filename, ?string $mime, string $path): string
    {
        $filename = trim($filename);
        if ($filename === '') {
            $filename = 'submission';
        }

        $currentExt = pathinfo($filename, PATHINFO_EXTENSION);
        if ($currentExt !== '') {
            return $filename;
        }

        $pathExt = pathinfo($path, PATHINFO_EXTENSION);
        $ext = $pathExt !== '' ? $pathExt : $this->extensionFromMime($mime);
        if ($ext !== '') {
            return $filename.'.'.$ext;
        }

        return $filename;
    }

    private function extensionFromMime(?string $mime): string
    {
        $mime = strtolower((string) $mime);

        return match ($mime) {
            'application/zip' => 'zip',
            'application/x-zip-compressed' => 'zip',
            'application/x-rar-compressed' => 'rar',
            'application/vnd.rar' => 'rar',
            'application/x-7z-compressed' => '7z',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.ms-powerpoint' => 'ppt',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'video/mp4' => 'mp4',
            'video/quicktime' => 'mov',
            'video/x-msvideo' => 'avi',
            'video/x-matroska' => 'mkv',
            'text/plain' => 'txt',
            'text/csv' => 'csv',
            'application/rtf' => 'rtf',
            default => '',
        };
    }

    private function storeResourceFile($file): array
    {
        $safeName = preg_replace('/[^a-zA-Z0-9._-]+/', '-', $file->getClientOriginalName()) ?: 'demo-task-resource';
        $path = $file->storeAs(
            'demo-task-resources',
            uniqid('resource_', true).'-'.$safeName
        );

        return [
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ];
    }

    private function storeTaskVideo($file): array
    {
        $safeName = preg_replace('/[^a-zA-Z0-9._-]+/', '-', $file->getClientOriginalName()) ?: 'demo-task-video';
        $path = $file->storeAs(
            'demo-task-videos',
            uniqid('task_video_', true).'-'.$safeName
        );

        return [
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ];
    }

    private function taskVideoRules(): array
    {
        if (! $this->supportsTaskVideo()) {
            return ['nullable'];
        }

        return [
            'nullable',
            'file',
            'mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-matroska,video/webm',
            'max:512000',
        ];
    }

    private function supportsTaskVideo(): bool
    {
        return Schema::hasColumn('demo_tasks', 'task_video_path');
    }
}
