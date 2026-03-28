<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseItemSubmission extends Model
{
    use HasFactory;

    public const STATUS_PENDING_REVIEW = 'pending_review';
    public const STATUS_REVIEWED = 'reviewed';
    public const STATUS_REVISION_REQUESTED = 'revision_requested';

    public const REVIEW_STATUSES = [
        self::STATUS_PENDING_REVIEW,
        self::STATUS_REVIEWED,
        self::STATUS_REVISION_REQUESTED,
    ];

    protected $fillable = [
        'course_enrollment_id',
        'course_session_item_id',
        'submitted_by',
        'submission_type',
        'answer_text',
        'file_path',
        'file_name',
        'file_mime',
        'file_size',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'review_status',
        'review_notes',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function reviewStatusOptions(): array
    {
        return [
            self::STATUS_PENDING_REVIEW => 'Pending Review',
            self::STATUS_REVIEWED => 'Reviewed',
            self::STATUS_REVISION_REQUESTED => 'Revision Requested',
        ];
    }

    public function reviewStatusLabel(): string
    {
        return self::reviewStatusOptions()[$this->review_status] ?? 'Pending Review';
    }

    public function reviewStatusTone(): string
    {
        return match ($this->review_status) {
            self::STATUS_REVIEWED => 'done',
            self::STATUS_REVISION_REQUESTED => 'revision',
            default => 'pending',
        };
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(CourseEnrollment::class, 'course_enrollment_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(CourseSessionItem::class, 'course_session_item_id');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
