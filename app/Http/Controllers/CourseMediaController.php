<?php

namespace App\Http\Controllers;

use App\Models\CourseSessionItem;
use App\Models\CourseEnrollment;
use App\Models\User;
use App\Services\CloudinaryPrivateMediaService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CourseMediaController extends Controller
{
    public function view(Request $request, CourseSessionItem $item): View
    {
        $this->authorizeItemAccess($request, $item);
        abort_unless($item->hasPrivateCloudinaryAsset(), 404, 'Secure media not available for this item.');

        $cloudinary = app(CloudinaryPrivateMediaService::class);
        abort_unless($cloudinary->isConfigured(), 500, 'Cloudinary is not configured.');

        $format = Str::lower((string) $item->cloudinary_format);
        $isVideo = $item->cloudinary_resource_type === 'video';
        $isPdf = $format === 'pdf';

        $deliveryFormat = $item->cloudinary_resource_type === 'video'
            ? 'mp4'
            : $item->cloudinary_format;

        $directUrl = $cloudinary->temporaryAccessUrl(
            $item->cloudinary_public_id,
            $deliveryFormat,
            $item->cloudinary_resource_type,
            $item->cloudinary_delivery_type ?: 'authenticated'
        );

        return view('courses.secure-media-viewer', [
            'item' => $item,
            'streamUrl' => route('course-session-items.media.stream', $item),
            'directUrl' => $directUrl,
            'isVideo' => $isVideo,
            'isPdf' => $isPdf,
        ]);
    }

    public function stream(Request $request, CourseSessionItem $item): Response|StreamedResponse
    {
        $this->authorizeItemAccess($request, $item);
        abort_unless($item->hasPrivateCloudinaryAsset(), 404, 'Secure media not available for this item.');

        $cloudinary = app(CloudinaryPrivateMediaService::class);
        abort_unless($cloudinary->isConfigured(), 500, 'Cloudinary is not configured.');

        $deliveryFormat = $item->cloudinary_resource_type === 'video'
            ? 'mp4'
            : $item->cloudinary_format;

        $signedUrl = $cloudinary->temporaryAccessUrl(
            $item->cloudinary_public_id,
            $deliveryFormat,
            $item->cloudinary_resource_type,
            $item->cloudinary_delivery_type ?: 'authenticated'
        );

        $forwardHeaders = [];
        if ($request->hasHeader('Range')) {
            $forwardHeaders['Range'] = (string) $request->header('Range');
        }

        try {
            $upstream = Http::withOptions([
                'stream' => true,
                'http_errors' => false,
                'connect_timeout' => 10,
                'timeout' => 120,
            ])->withHeaders($forwardHeaders)->get($signedUrl);
        } catch (\Throwable $e) {
            return redirect()->away($signedUrl);
        }

        $status = $upstream->status();
        if (in_array($status, [401, 403, 404], true)) {
            abort(404, 'File is unavailable.');
        }
        if ($status >= 400) {
            abort(502, 'Unable to load secure media right now.');
        }

        $contentType = (string) ($upstream->header('Content-Type') ?: 'application/octet-stream');
        $contentLength = $upstream->header('Content-Length');
        $contentRange = $upstream->header('Content-Range');
        $acceptRanges = $upstream->header('Accept-Ranges');
        $filename = $this->streamFilename($item);
        $disposition = $request->boolean('download')
            ? 'attachment'
            : 'inline';

        $headers = [
            'Content-Type' => $contentType,
            'Content-Disposition' => $disposition.'; filename="'.$filename.'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        if ($contentLength) {
            $headers['Content-Length'] = $contentLength;
        }
        if ($contentRange) {
            $headers['Content-Range'] = $contentRange;
        }
        if ($acceptRanges) {
            $headers['Accept-Ranges'] = $acceptRanges;
        }

        $psrBody = $upstream->toPsrResponse()->getBody();

        return response()->stream(function () use ($psrBody): void {
            while (! $psrBody->eof()) {
                echo $psrBody->read(8192);
                flush();
            }
        }, $status, $headers);
    }

    private function authorizeItemAccess(Request $request, CourseSessionItem $item): void
    {
        $user = $request->user();
        abort_unless($user, 403);

        if (in_array($user->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN, User::ROLE_MANAGER_HR, User::ROLE_IT], true)) {
            return;
        }

        $courseId = (int) optional(optional($item->session)->week)->course_id;
        abort_unless($courseId > 0, 404);

        if ($user->role === User::ROLE_STUDENT) {
            $isEnrolled = CourseEnrollment::where('course_id', $courseId)
                ->where('student_id', $user->id)
                ->exists();

            abort_if(! $isEnrolled, 403, 'You can open only enrolled course files.');
            return;
        }

        if ($user->role === User::ROLE_TRAINER) {
            $isAssigned = CourseEnrollment::where('course_id', $courseId)
                ->where('trainer_id', $user->id)
                ->exists();

            abort_if(! $isAssigned, 403, 'You can open only assigned course files.');
            return;
        }

        abort(403);
    }

    private function streamFilename(CourseSessionItem $item): string
    {
        $base = Str::slug($item->title ?: 'course-resource');
        $extension = Str::lower((string) $item->cloudinary_format);
        $extension = preg_replace('/[^a-z0-9]/', '', $extension) ?: 'bin';

        return $base.'.'.$extension;
    }
}
