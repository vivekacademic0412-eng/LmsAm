<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'title',
        'short_description',
        'slug',
        'description',
        'language',
        'thumbnail',
        'duration_hours',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'duration_hours' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class, 'subcategory_id');
    }

    public function days(): HasMany
    {
        return $this->hasMany(CourseDay::class, 'course_id')->orderBy('day_number');
    }

    public function weeks(): HasMany
    {
        return $this->hasMany(CourseWeek::class, 'course_id')->orderBy('week_number');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class, 'course_id');
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if (! $this->thumbnail) {
            return null;
        }

        if (Str::startsWith($this->thumbnail, ['http://', 'https://'])) {
            return $this->thumbnail;
        }

        return asset('storage/'.ltrim($this->thumbnail, '/'));
    }
}
