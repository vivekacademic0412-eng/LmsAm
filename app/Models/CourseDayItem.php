<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseDayItem extends Model
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
        'course_day_id',
        'item_type',
        'title',
        'resource_type',
        'content',
        'resource_url',
        'cloudinary_public_id',
        'cloudinary_resource_type',
        'cloudinary_format',
        'cloudinary_delivery_type',
    ];

    public function hasPrivateCloudinaryAsset(): bool
    {
        return (bool) $this->cloudinary_public_id
            && (bool) $this->cloudinary_resource_type
            && (bool) $this->cloudinary_format;
    }

    public function day(): BelongsTo
    {
        return $this->belongsTo(CourseDay::class, 'course_day_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(CourseProgress::class, 'course_day_item_id');
    }
}
