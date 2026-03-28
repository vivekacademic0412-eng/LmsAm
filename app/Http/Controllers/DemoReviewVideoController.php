<?php

namespace App\Http\Controllers;

use App\Models\DemoReviewVideo;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DemoReviewVideoController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $videos = $this->orderedVideos();
        $featured = $videos->first();
        $nextPosition = ((int) ($videos->max('position') ?? 0)) + 1;

        return view('demo.review-videos.index', [
            'videos' => $videos,
            'featured' => $featured,
            'nextPosition' => $nextPosition,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $this->ensureReviewVideosTableExists();

        $data = $request->validate([
            'position' => $this->positionValidationRules(),
            'title' => ['nullable', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:2000'],
            'video_url' => ['required', 'url', 'max:500'],
        ]);

        $youtubeId = $this->extractYouTubeId($data['video_url']);
        if (! $youtubeId) {
            throw ValidationException::withMessages([
                'video_url' => 'Enter a valid YouTube video link.',
            ]);
        }

        DemoReviewVideo::create([
            'position' => $this->resolvePosition($data['position'] ?? null),
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'youtube_url' => $data['video_url'],
            'youtube_id' => $youtubeId,
            'uploaded_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Review video added.');
    }

    public function update(Request $request, DemoReviewVideo $video): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $this->ensureReviewVideosTableExists();

        $data = $request->validate([
            'position' => $this->positionValidationRules($video),
            'title' => ['nullable', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:2000'],
            'video_url' => ['required', 'url', 'max:500'],
        ]);

        $youtubeId = $this->extractYouTubeId($data['video_url']);
        if (! $youtubeId) {
            throw ValidationException::withMessages([
                'video_url' => 'Enter a valid YouTube video link.',
            ]);
        }

        $video->update([
            'position' => $this->resolvePosition($data['position'] ?? null, $video),
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'youtube_url' => $data['video_url'],
            'youtube_id' => $youtubeId,
        ]);

        return back()->with('success', 'Review video updated.');
    }

    public function destroy(Request $request, DemoReviewVideo $video): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $this->ensureReviewVideosTableExists();

        $video->delete();

        return back()->with('success', 'Review video deleted.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(
            in_array($request->user()?->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true),
            403
        );
    }

    /**
     * @return Collection<int, DemoReviewVideo>
     */
    private function orderedVideos(): Collection
    {
        if (! Schema::hasTable('demo_review_videos')) {
            return collect();
        }

        return DemoReviewVideo::query()
            ->with('uploader:id,name')
            ->orderByRaw('CASE WHEN position IS NULL THEN 1 ELSE 0 END')
            ->orderBy('position')
            ->orderByDesc('id')
            ->get();
    }

    private function ensureReviewVideosTableExists(): void
    {
        if (! Schema::hasTable('demo_review_videos')) {
            abort(503, 'The demo review videos table has not been migrated yet.');
        }
    }

    private function resolvePosition(?int $requestedPosition, ?DemoReviewVideo $video = null): int
    {
        if ($requestedPosition !== null) {
            return $requestedPosition;
        }

        if ($video && $video->position) {
            return $video->position;
        }

        return ((int) DemoReviewVideo::max('position')) + 1;
    }

    private function positionValidationRules(?DemoReviewVideo $video = null): array
    {
        $rules = ['nullable', 'integer', 'min:1', 'max:9999'];

        if ($video) {
            $rules[] = Rule::unique('demo_review_videos', 'position')->ignore($video->id);
        } else {
            $rules[] = 'unique:demo_review_videos,position';
        }

        return $rules;
    }

    private function extractYouTubeId(string $url): ?string
    {
        $parts = parse_url(trim($url));
        if (! is_array($parts)) {
            return null;
        }

        $host = strtolower((string) ($parts['host'] ?? ''));
        $path = trim((string) ($parts['path'] ?? ''), '/');

        if ($host === 'youtu.be') {
            return $this->sanitizeYouTubeId(strtok($path, '/'));
        }

        if (str_contains($host, 'youtube.com') || str_contains($host, 'youtube-nocookie.com')) {
            parse_str((string) ($parts['query'] ?? ''), $query);

            if (! empty($query['v'])) {
                return $this->sanitizeYouTubeId((string) $query['v']);
            }

            foreach (['embed/', 'shorts/', 'live/'] as $prefix) {
                if (str_starts_with($path, $prefix)) {
                    return $this->sanitizeYouTubeId(substr($path, strlen($prefix)));
                }
            }
        }

        return null;
    }

    private function sanitizeYouTubeId(?string $value): ?string
    {
        $candidate = trim((string) $value);

        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $candidate) === 1) {
            return $candidate;
        }

        return null;
    }
}
