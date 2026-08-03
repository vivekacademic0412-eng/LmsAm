<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CourseViewLog extends Model
{
    protected $fillable = [
        'tid',
        'user_id',
        'course_id',
        'course_session_item_id',
        'event',
        'ip_address',
        'user_agent',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $log) {
            $log->tid ??= (string) Str::uuid();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(CourseSessionItem::class, 'course_session_item_id');
    }
}