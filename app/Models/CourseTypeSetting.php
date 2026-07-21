<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseTypeSetting extends Model
{
    //
    protected $fillable = [
        'course_type_id',
        'exception_days',
        'completion_threshold_percent',
        'min_session_percent',
        'max_weeks',
        'certificate_mode',
        'allow_pause',
    ];
 
    protected $casts = [
        'allow_pause' => 'boolean',
    ];
 
    public function courseType(): BelongsTo
    {
        return $this->belongsTo(CourseType::class);
    }
}
