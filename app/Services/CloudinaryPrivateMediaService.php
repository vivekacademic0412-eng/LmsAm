<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use RuntimeException;

class CloudinaryPrivateMediaService
{
    private const DELIVERY_TYPE = 'upload';
    private const UPLOAD_TIMEOUT_SECONDS = 1200;
    private const CHUNK_SIZE_BYTES = 10000000;

    public function isConfigured(): bool
    {
        $cloudName = (string) (env('CLOUDINARY_CLOUD_NAME') ?: config('services.cloudinary.cloud_name'));
        $apiKey = (string) (env('CLOUDINARY_API_KEY') ?: config('services.cloudinary.api_key'));
        $apiSecret = (string) (env('CLOUDINARY_API_SECRET') ?: config('services.cloudinary.api_secret'));

        return $cloudName !== '' && $apiKey !== '' && $apiSecret !== '';
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

    public function deleteAsset(string $publicId, string $resourceType = 'raw', string $deliveryType = self::DELIVERY_TYPE): void
    {
        if (! $this->isConfigured()) {
            return;
        }

        try {
            $this->client()->uploadApi()->destroy($publicId, [
                'resource_type' => $resourceType,
                'type' => $deliveryType,
            ]);
        } catch (\Throwable $e) {
            // Best-effort delete; ignore failures.
        }
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
        return $this->accessUrl($publicId, $format, $resourceType, $deliveryType);
    }

    public function accessUrl(
        string $publicId,
        string $format,
        string $resourceType = 'raw',
        string $deliveryType = self::DELIVERY_TYPE
    ): string {
        if ($deliveryType !== self::DELIVERY_TYPE) {
            return $this->signedAccessUrl($publicId, $format, $resourceType, $deliveryType);
        }

        return $this->publicAccessUrl($publicId, $format, $resourceType, $deliveryType);
    }

    public function publicAccessUrl(
        string $publicId,
        string $format,
        string $resourceType = 'raw',
        string $deliveryType = self::DELIVERY_TYPE
    ): string {
        $asset = $resourceType === 'video'
            ? $this->client()->video($publicId)
            : $this->client()->raw($publicId);

        $asset->deliveryType($deliveryType);
        $asset->signUrl(false);

        $lastSegment = Str::afterLast($publicId, '/');
        $hasExtension = Str::contains($lastSegment, '.');
        if ($format !== '' && ! $hasExtension && ! Str::endsWith($publicId, '.'.$format)) {
            $asset->extension($format);
        }

        return (string) $asset->toUrl();
    }

    public function signedAccessUrl(
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

        $lastSegment = Str::afterLast($publicId, '/');
        $hasExtension = Str::contains($lastSegment, '.');
        if ($format !== '' && ! $hasExtension && ! Str::endsWith($publicId, '.'.$format)) {
            $asset->extension($format);
        }

        return (string) $asset->toUrl();
    }

    private function client(): Cloudinary
    {
        $cloudName = (string) (env('CLOUDINARY_CLOUD_NAME') ?: config('services.cloudinary.cloud_name'));
        $apiKey = (string) (env('CLOUDINARY_API_KEY') ?: config('services.cloudinary.api_key'));
        $apiSecret = (string) (env('CLOUDINARY_API_SECRET') ?: config('services.cloudinary.api_secret'));

        $config = [
            'cloud' => [
                'cloud_name' => $cloudName,
                'api_key' => $apiKey,
                'api_secret' => $apiSecret,
            ],
            'url' => [
                'secure' => true,
            ],
        ];

        $authTokenKey = (string) config('services.cloudinary.auth_token_key');
        if ($authTokenKey !== '') {
            $config['auth_token'] = [
                'key' => $authTokenKey,
                'duration' => (int) config('services.cloudinary.auth_token_ttl', 600),
            ];
        }

        return new Cloudinary($config);
    }
}
