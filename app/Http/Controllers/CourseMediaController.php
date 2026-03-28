<?php

namespace App\Http\Controllers;

use App\Models\CourseSessionItem;
use App\Models\CourseEnrollment;
use App\Models\CourseProgress;
use App\Models\User;
use App\Services\CloudinaryPrivateMediaService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CourseMediaController extends Controller
{
    public function view(Request $request, CourseSessionItem $item): View
    {
        $this->authorizeItemAccess($request, $item);
        $this->markProgressIfStudent($request, $item);
        $allowDownload = in_array($item->item_type, [CourseSessionItem::TYPE_TASK, CourseSessionItem::TYPE_QUIZ], true);
        if ($item->hasPrivateCloudinaryAsset()) {
            $format = Str::lower((string) $item->cloudinary_format);
            $isVideo = $item->cloudinary_resource_type === 'video';
            $isPdf = $format === 'pdf';
            $isDocx = $format === 'docx';
            $isPpt = $format === 'ppt';
            $isPptx = $format === 'pptx';
            $isOfficeDoc = in_array($format, ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'], true);
            $isImage = in_array($format, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp'], true);
            $isAudio = in_array($format, ['mp3', 'wav', 'ogg', 'm4a', 'aac'], true);

            $deliveryFormat = $item->cloudinary_resource_type === 'video'
                ? 'mp4'
                : $item->cloudinary_format;

            $cloudinary = app(CloudinaryPrivateMediaService::class);
            abort_unless($cloudinary->isConfigured(), 500, 'Cloudinary is not configured.');

            $directUrl = $cloudinary->accessUrl(
                $item->cloudinary_public_id,
                $deliveryFormat,
                $item->cloudinary_resource_type,
                $item->cloudinary_delivery_type ?: 'upload'
            );
            $downloadUrl = $allowDownload
                ? route('course-session-items.media.download', $item)
                : null;
            $documentViewerUrl = $isOfficeDoc
                ? 'https://docs.google.com/gview?embedded=1&url='.urlencode($directUrl)
                : null;
        } elseif (! empty($item->resource_url)) {
            return redirect()->away($item->resource_url);
        } else {
            abort(404, 'Secure media not available for this item.');
        }

        $viewName = $request->boolean('embed')
            ? 'courses.secure-media-viewer-embed'
            : 'courses.secure-media-viewer';

        return view($viewName, [
            'item' => $item,
            'streamUrl' => route('course-session-items.media.stream', $item),
            'directUrl' => $directUrl,
            'downloadUrl' => $downloadUrl ?? null,
            'allowDownload' => $allowDownload,
            'isVideo' => $isVideo,
            'isPdf' => $isPdf,
            'isDocx' => $isDocx ?? false,
            'isPpt' => $isPpt ?? false,
            'isPptx' => $isPptx ?? false,
            'isImage' => $isImage ?? false,
            'isAudio' => $isAudio ?? false,
            'videoStream' => $isVideo ? route('course-session-items.media.stream', $item) : null,
            'isOfficeDoc' => $isOfficeDoc ?? false,
            'documentViewerUrl' => $documentViewerUrl ?? null,
        ]);
    }

    public function stream(Request $request, CourseSessionItem $item): Response|StreamedResponse
    {
        $this->authorizeItemAccess($request, $item);
        $this->markProgressIfStudent($request, $item);
        if ($item->hasPrivateCloudinaryAsset()) {
            $cloudinary = app(CloudinaryPrivateMediaService::class);
            abort_unless($cloudinary->isConfigured(), 500, 'Cloudinary is not configured.');

            $deliveryFormat = $item->cloudinary_resource_type === 'video'
                ? 'mp4'
                : $item->cloudinary_format;

            $directUrl = $cloudinary->accessUrl(
                $item->cloudinary_public_id,
                $deliveryFormat,
                $item->cloudinary_resource_type,
                $item->cloudinary_delivery_type ?: 'upload'
            );

            $format = Str::lower((string) $item->cloudinary_format);
            if ($item->cloudinary_resource_type === 'raw' && $format === 'pdf') {
                return $this->proxyInlineStream($request, $directUrl, 'application/pdf', $this->streamFilename($item), true);
            }

            if ($item->cloudinary_resource_type === 'raw' && $format === 'docx') {
                return $this->proxyInlineStream(
                    $request,
                    $directUrl,
                    $this->contentTypeForFormat($format),
                    $this->streamFilename($item),
                    false
                );
            }

            if ($item->cloudinary_resource_type === 'raw' && $format === 'pptx') {
                return $this->proxyInlineStream(
                    $request,
                    $directUrl,
                    $this->contentTypeForFormat($format),
                    $this->streamFilename($item),
                    false
                );
            }

            if ($item->cloudinary_resource_type === 'video') {
                return $this->proxyInlineStream($request, $directUrl, 'video/mp4', $this->streamFilename($item), true);
            }

            return redirect()->away($directUrl);
        }

        if (! empty($item->resource_url)) {
            return redirect()->away($item->resource_url);
        }

        abort(404, 'Secure media not available for this item.');
    }

    public function download(Request $request, CourseSessionItem $item)
    {
        $this->authorizeItemAccess($request, $item);
        abort_unless(in_array($item->item_type, [CourseSessionItem::TYPE_TASK, CourseSessionItem::TYPE_QUIZ], true), 403);

        if ($item->hasPrivateCloudinaryAsset()) {
            $cloudinary = app(CloudinaryPrivateMediaService::class);
            abort_unless($cloudinary->isConfigured(), 500, 'Cloudinary is not configured.');

            $format = $item->cloudinary_format ?: 'bin';
            $downloadUrl = $cloudinary->temporaryDownloadUrl(
                $item->cloudinary_public_id,
                $format,
                $item->cloudinary_resource_type ?: 'raw',
                $item->cloudinary_delivery_type ?: 'upload'
            );

            return $this->proxyDownload(
                $downloadUrl,
                $this->downloadFilename($item),
                $this->contentTypeForFormat($format)
            );
        }

        if (! empty($item->resource_url)) {
            return redirect()->away($item->resource_url);
        }

        abort(404, 'Secure media not available for this item.');
    }


    private function downloadFilename(CourseSessionItem $item): string
    {
        $base = Str::slug($item->title ?: 'course-file');
        $extension = Str::lower((string) $item->cloudinary_format);
        $extension = preg_replace('/[^a-z0-9]/', '', $extension) ?: 'bin';

        return $base.'.'.$extension;
    }

    private function contentTypeForFormat(string $format): string
    {
        return match (Str::lower($format)) {
            'pdf' => 'application/pdf',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            '7z' => 'application/x-7z-compressed',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'mp4' => 'video/mp4',
            'mp3' => 'audio/mpeg',
            default => 'application/octet-stream',
        };
    }

    private function proxyDownload(string $url, string $filename, string $contentType): StreamedResponse
    {
        return response()->stream(function () use ($url): void {
            if (function_exists('curl_init')) {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_BUFFERSIZE, 1024 * 1024);
                curl_setopt($ch, CURLOPT_TIMEOUT, 0);
                curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data): int {
                    echo $data;
                    flush();
                    return strlen($data);
                });
                curl_exec($ch);
                curl_close($ch);
                return;
            }

            $context = stream_context_create([
                'http' => [
                    'timeout' => 60,
                ],
                'ssl' => [
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                ],
            ]);

            $handle = @fopen($url, 'rb', false, $context);
            if ($handle === false) {
                return;
            }

            while (! feof($handle)) {
                echo fread($handle, 1024 * 1024);
                flush();
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
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

    private function markProgressIfStudent(Request $request, CourseSessionItem $item): void
    {
        $user = $request->user();
        if (! $user || $user->role !== User::ROLE_STUDENT) {
            return;
        }

        $courseId = (int) optional(optional($item->session)->week)->course_id;
        if ($courseId <= 0) {
            return;
        }

        $enrollment = CourseEnrollment::where('course_id', $courseId)
            ->where('student_id', $user->id)
            ->first();

        if (! $enrollment) {
            return;
        }

        CourseProgress::updateOrCreate(
            [
                'course_enrollment_id' => $enrollment->id,
                'course_session_item_id' => $item->id,
            ],
            [
                'completed_at' => now(),
            ]
        );
    }

    private function streamFilename(CourseSessionItem $item): string
    {
        $base = Str::slug($item->title ?: 'course-resource');
        $extension = Str::lower((string) $item->cloudinary_format);
        $extension = preg_replace('/[^a-z0-9]/', '', $extension) ?: 'bin';

        return $base.'.'.$extension;
    }

    private function proxyInlineStream(Request $request, string $url, string $contentType, string $filename, bool $acceptRanges): StreamedResponse
    {
        $rangeHeader = $request->header('Range');
        $totalSize = $acceptRanges ? $this->fetchContentLength($url) : null;
        $range = $acceptRanges ? $this->parseRange($rangeHeader, $totalSize) : null;

        $status = 200;
        $headers = [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'X-Content-Type-Options' => 'nosniff',
        ];

        if ($acceptRanges) {
            $headers['Accept-Ranges'] = 'bytes';
        }

        if ($range && $totalSize !== null) {
            $start = $range['start'];
            $end = $range['end'];
            $length = $end - $start + 1;
            $headers['Content-Range'] = "bytes {$start}-{$end}/{$totalSize}";
            $headers['Content-Length'] = (string) $length;
            $status = 206;
        } elseif ($totalSize !== null) {
            $headers['Content-Length'] = (string) $totalSize;
        }

        return response()->stream(function () use ($url, $range): void {
            if (function_exists('curl_init')) {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_BUFFERSIZE, 1024 * 1024);
                curl_setopt($ch, CURLOPT_TIMEOUT, 0);
                if ($range) {
                    curl_setopt($ch, CURLOPT_RANGE, $range['start'].'-'.$range['end']);
                }
                curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data): int {
                    echo $data;
                    flush();
                    return strlen($data);
                });
                curl_exec($ch);
                curl_close($ch);
                return;
            }

            $context = stream_context_create([
                'http' => [
                    'timeout' => 60,
                    'header' => $range ? "Range: bytes={$range['start']}-{$range['end']}\r\n" : '',
                ],
                'ssl' => [
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                ],
            ]);

            $handle = @fopen($url, 'rb', false, $context);
            if ($handle === false) {
                return;
            }

            while (! feof($handle)) {
                echo fread($handle, 1024 * 1024);
                flush();
            }

            fclose($handle);
        }, $status, $headers);
    }

    private function fetchContentLength(string $url): ?int
    {
        if (! function_exists('curl_init')) {
            return null;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_exec($ch);
        $length = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        curl_close($ch);

        if ($length === -1.0 || $length === false) {
            return null;
        }

        return (int) $length;
    }

    /**
     * @return array{start:int,end:int}|null
     */
    private function parseRange(?string $rangeHeader, ?int $totalSize): ?array
    {
        if (! $rangeHeader || ! $totalSize) {
            return null;
        }

        if (! preg_match('/bytes=(\d*)-(\d*)/i', $rangeHeader, $matches)) {
            return null;
        }

        $start = $matches[1] === '' ? null : (int) $matches[1];
        $end = $matches[2] === '' ? null : (int) $matches[2];

        if ($start === null && $end === null) {
            return null;
        }

        if ($start === null) {
            $start = max(0, $totalSize - $end);
            $end = $totalSize - 1;
        } elseif ($end === null || $end >= $totalSize) {
            $end = $totalSize - 1;
        }

        if ($start > $end) {
            return null;
        }

        return [
            'start' => $start,
            'end' => $end,
        ];
    }
}
