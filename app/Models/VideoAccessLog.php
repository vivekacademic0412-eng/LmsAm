<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * IMPORTANT — what this can and cannot detect:
 * No website/browser can detect OS-level screen recording (Windows Game Bar, OBS,
 * phone screen-record, another camera pointed at the screen, etc.) — this is a
 * hard technical limitation of the web platform, not something to be worked around.
 *
 * What we CAN capture are behavioral signals that often correlate with someone
 * trying to save/rip content: switching tabs mid-video, opening devtools, blocking
 * right-click, exiting fullscreen repeatedly. These are logged here as proxies for
 * admin review — NOT as proof of piracy. Treat SUSPICIOUS_EVENTS as "worth a look",
 * not "confirmed violation".
 */
class VideoAccessLog extends Model
{
    protected $fillable = [
        'user_id', 'course_session_item_id', 'event', 'meta', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    const SUSPICIOUS_EVENTS = [
        'devtools_suspected',
        'right_click_blocked',
        'fullscreen_exit',
        'tab_hidden',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function item(): BelongsTo { return $this->belongsTo(CourseSessionItem::class, 'course_session_item_id'); }
}