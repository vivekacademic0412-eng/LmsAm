<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'course_week_id',
        'certificate_number',
        'file_path',
        'issued_at',
        'downloaded_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'downloaded_at' => 'datetime',
    ];

    /**
     * Enrollment for which this certificate was issued.
     */
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Week certificate (nullable).
     * Null means this is the final course certificate.
     */
    public function courseWeek()
    {
        return $this->belongsTo(CourseWeek::class);
    }

    /**
     * Check whether this is the final course certificate.
     */
    public function isFinalCertificate(): bool
    {
        return is_null($this->course_week_id);
    }

    /**
     * Check whether this is a week-wise certificate.
     */
    public function isWeeklyCertificate(): bool
    {
        return ! is_null($this->course_week_id);
    }
}