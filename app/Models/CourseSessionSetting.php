<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseSessionSetting extends Model
{
    protected $fillable = [
        'course_session_id',
        'is_required_for_certificate',
        'meet_link',
        'meet_datetime',
        'is_visible',
    ];

    protected $casts = [
        'is_required_for_certificate' => 'boolean',
        'is_visible'                  => 'boolean',
        'meet_datetime'                => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(CourseSession::class, 'course_session_id');
    }

    /** True if the meet link should be shown as "joinable" right now (+/- 15 min window) */
    public function isJoinableNow(): bool
    {
        if (!$this->meet_link || !$this->meet_datetime) {
            return false;
        }

        return now()->between(
            $this->meet_datetime->copy()->subMinutes(15),
            $this->meet_datetime->copy()->addMinutes(90)
        );
    }
}