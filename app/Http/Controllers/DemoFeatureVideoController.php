<?php

namespace App\Http\Controllers;

use App\Models\DemoFeatureVideo;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DemoFeatureVideoController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $videos = $this->orderedVideos()->get();
        $featured = $videos->first();
        $nextPosition = $this->usesPositionColumn()
            ? ((int) ($videos->max('position') ?? 0)) + 1
            : $videos->count() + 1;

        return view('demo.feature-video.index', [
            'videos' => $videos,
            'featured' => $featured,
            'nextPosition' => $nextPosition,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $data = $request->validate([
            'position' => $this->positionValidationRules(),
            'title' => ['nullable', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:2000'],
            'video_file' => ['required', 'file', 'mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-matroska,video/webm', 'max:512000'],
        ]);

        $file = $request->file('video_file');
        $safeName = preg_replace('/[^a-zA-Z0-9._-]+/', '-', $file->getClientOriginalName()) ?: 'demo-feature-video';
        $path = $file->storeAs(
            'demo-feature-video',
            uniqid('feature_', true).'-'.$safeName
        );

        $attributes = [
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_mime' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => $request->user()->id,
        ];

        if ($this->usesPositionColumn()) {
            $attributes['position'] = $this->resolvePosition($data['position'] ?? null);
        }

        DemoFeatureVideo::create($attributes);

        return back()->with('success', 'Feature video uploaded.');
    }

    public function destroy(Request $request, DemoFeatureVideo $video): RedirectResponse
    {
        $this->authorizeAdmin($request);

        if ($video->file_path && Storage::disk('local')->exists($video->file_path)) {
            Storage::disk('local')->delete($video->file_path);
        }

        $video->delete();

        return back()->with('success', 'Feature video deleted.');
    }

    public function update(Request $request, DemoFeatureVideo $video): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $data = $request->validate([
            'position' => $this->positionValidationRules($video),
            'title' => ['nullable', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:2000'],
            'video_file' => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-matroska,video/webm', 'max:512000'],
        ]);

        if ($request->hasFile('video_file')) {
            $file = $request->file('video_file');
            $safeName = preg_replace('/[^a-zA-Z0-9._-]+/', '-', $file->getClientOriginalName()) ?: 'demo-feature-video';
            $path = $file->storeAs(
                'demo-feature-video',
                uniqid('feature_', true).'-'.$safeName
            );

            if ($video->file_path && Storage::disk('local')->exists($video->file_path)) {
                Storage::disk('local')->delete($video->file_path);
            }

            $video->file_path = $path;
            $video->file_name = $file->getClientOriginalName();
            $video->file_mime = $file->getClientMimeType();
            $video->file_size = $file->getSize();
        }

        if ($this->usesPositionColumn()) {
            $video->position = $this->resolvePosition($data['position'] ?? null, $video);
        }
        $video->title = $data['title'] ?? null;
        $video->description = $data['description'] ?? null;
        $video->save();

        return back()->with('success', 'Feature video updated.');
    }

    public function show(Request $request, DemoFeatureVideo $video)
    {
        $user = $request->user();
        abort_unless($user, 403);

        if (! in_array($user->role, [User::ROLE_DEMO, User::ROLE_ADMIN, User::ROLE_SUPERADMIN], true)) {
            abort(403);
        }

        if (! $video->file_path || ! Storage::disk('local')->exists($video->file_path)) {
            abort(404);
        }

        $path = Storage::disk('local')->path($video->file_path);
        $filename = $video->file_name ?: basename($video->file_path);
        $mime = $video->file_mime ?: 'video/mp4';

        return response()->file($path, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(
            in_array($request->user()?->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true),
            403
        );
    }

    private function orderedVideos()
    {
        $query = DemoFeatureVideo::query();

        if (! $this->usesPositionColumn()) {
            return $query->latest('id');
        }

        return $query
            ->orderByRaw('CASE WHEN position IS NULL THEN 1 ELSE 0 END')
            ->orderBy('position')
            ->orderByDesc('id');
    }

    private function resolvePosition(?int $requestedPosition, ?DemoFeatureVideo $video = null): int
    {
        if (! $this->usesPositionColumn()) {
            return 0;
        }

        if ($requestedPosition !== null) {
            return $requestedPosition;
        }

        if ($video && $video->position) {
            return $video->position;
        }

        return ((int) DemoFeatureVideo::max('position')) + 1;
    }

    private function usesPositionColumn(): bool
    {
        return Schema::hasColumn('demo_feature_videos', 'position');
    }

    private function positionValidationRules(?DemoFeatureVideo $video = null): array
    {
        if (! $this->usesPositionColumn()) {
            return ['nullable'];
        }

        $rules = ['nullable', 'integer', 'min:1', 'max:9999'];

        if ($video) {
            $rules[] = Rule::unique('demo_feature_videos', 'position')->ignore($video->id);
        } else {
            $rules[] = 'unique:demo_feature_videos,position';
        }

        return $rules;
    }
}
