<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseSessionItem extends Model
{
    use HasFactory;

    public const TYPE_INTRO = 'intro';
    public const TYPE_MAIN_VIDEO = 'main_video';
    public const TYPE_TASK = 'task';
    public const TYPE_QUIZ = 'quiz';

    public const TYPES = [
        self::TYPE_INTRO,
        self::TYPE_MAIN_VIDEO,
        self::TYPE_TASK,
        self::TYPE_QUIZ,
    ];

    protected $fillable = [
        'course_session_id',
        'item_type',
        'title',
        'resource_type',
        'content',
        'resource_url',
        'is_live',
        'live_at',
        'cloudinary_public_id',
        'cloudinary_resource_type',
        'cloudinary_format',
        'cloudinary_delivery_type',
    ];

    protected function casts(): array
    {
        return [
            'is_live' => 'boolean',
            'live_at' => 'datetime',
        ];
    }

    public function hasPrivateCloudinaryAsset(): bool
    {
        return (bool) $this->cloudinary_public_id
            && (bool) $this->cloudinary_resource_type
            && (bool) $this->cloudinary_format;
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(CourseSession::class, 'course_session_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(CourseProgress::class, 'course_session_item_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(CourseItemSubmission::class, 'course_session_item_id');
    }
}
