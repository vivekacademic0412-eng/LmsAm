<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use RuntimeException;

class CloudinaryPrivateMediaService
{
    private const DELIVERY_TYPE = 'authenticated';
    private const UPLOAD_TIMEOUT_SECONDS = 1200;
    private const CHUNK_SIZE_BYTES = 10000000;

    public function isConfigured(): bool
    {
        return (bool) config('services.cloudinary.cloud_name')
            && (bool) config('services.cloudinary.api_key')
            && (bool) config('services.cloudinary.api_secret');
    }

    /**
     * @return array{
     *     public_id: string,
     *     resource_type: string,
     *     format: string,
     *     delivery_type: string
     * }
     */
    public function uploadPrivateCourseContent(UploadedFile $file): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Cloudinary is not configured.');
        }

        $resourceType = str_starts_with((string) $file->getMimeType(), 'video/') ? 'video' : 'raw';

        $result = $this->client()->uploadApi()->upload($file->getRealPath(), [
            'folder' => 'lms/course-session-items',
            'resource_type' => $resourceType,
            'type' => self::DELIVERY_TYPE,
            'access_mode' => self::DELIVERY_TYPE,
            'use_filename' => true,
            'unique_filename' => true,
            'overwrite' => true,
            'chunk_size' => self::CHUNK_SIZE_BYTES,
            'timeout' => self::UPLOAD_TIMEOUT_SECONDS,
            'upload_timeout' => self::UPLOAD_TIMEOUT_SECONDS,
        ]);

        return [
            'public_id' => (string) ($result['public_id'] ?? ''),
            'resource_type' => (string) ($result['resource_type'] ?? $resourceType),
            'format' => (string) ($result['format'] ?? $file->getClientOriginalExtension()),
            'delivery_type' => self::DELIVERY_TYPE,
        ];
    }

    public function temporaryDownloadUrl(
        string $publicId,
        string $format,
        string $resourceType = 'raw',
        string $deliveryType = self::DELIVERY_TYPE,
        int $ttlSeconds = 600
    ): string {
        $expiresAt = time() + max(60, $ttlSeconds);

        return $this->client()->uploadApi()->privateDownloadUrl($publicId, $format, [
            'resource_type' => $resourceType,
            'type' => $deliveryType,
            'expires_at' => $expiresAt,
        ]);
    }

    public function temporaryAccessUrl(
        string $publicId,
        string $format,
        string $resourceType = 'raw',
        string $deliveryType = self::DELIVERY_TYPE
    ): string {
        $asset = $resourceType === 'video'
            ? $this->client()->video($publicId)
            : $this->client()->raw($publicId);

        $asset->deliveryType($deliveryType);
        $asset->signUrl(true);

        if ($format !== '') {
            $asset->extension($format);
        }

        return (string) $asset->toUrl();
    }

    private function client(): Cloudinary
    {
        return new Cloudinary([
            'cloud' => [
                'cloud_name' => (string) config('services.cloudinary.cloud_name'),
                'api_key' => (string) config('services.cloudinary.api_key'),
                'api_secret' => (string) config('services.cloudinary.api_secret'),
            ],
            'url' => [
                'secure' => true,
            ],
        ]);
    }
}
