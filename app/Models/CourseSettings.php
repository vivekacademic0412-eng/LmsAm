<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseSettings extends Model
{
    protected $fillable = [
        'course_id',
        'min_completion_percent',
        'weekly_unlock_mode',
        'certificate_mode',
        'show_seats_as_full',
        'total_seats',
        'booked_seats',
        'zero_day_countdown_enabled',
        'countdown_days',
    ];

    protected $casts = [
        'show_seats_as_full'         => 'boolean',
        'zero_day_countdown_enabled' => 'boolean',
        'min_completion_percent'     => 'integer',
        'total_seats'                => 'integer',
        'booked_seats'               => 'integer',
        'countdown_days'             => 'integer',
    ];

    const UNLOCK_SEQUENTIAL = 'sequential_all_weeks';
    const UNLOCK_WEEK1_GATE = 'week1_gate_only';
    const UNLOCK_FREE       = 'free_no_lock';

    const CERT_END_OF_COURSE = 'end_of_course';
    const CERT_PER_WEEK      = 'per_week';
    const CERT_PER_LEVEL     = 'per_level';
    const CERT_BOTH          = 'both';

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /** Seats badge text for the frontend, e.g. "Seats Full" vs "12/40 booked" */
    public function seatsLabel(): string
    {
        if ($this->show_seats_as_full) {
            return 'Seats Full';
        }

        if ($this->total_seats) {
            return "{$this->booked_seats}/{$this->total_seats} Seats Booked";
        }

        return 'Open';
    }
}