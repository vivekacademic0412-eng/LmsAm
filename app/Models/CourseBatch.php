<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseBatch extends Model
{
    protected $table = 'course_batch';

    protected $fillable = [
        'course_id',
        'batch_id',
        'trainer_id',
        'status',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }
    public function student(): BelongsTo
    {
        return $this->belongsTo(BatchStudent::class);
    }


    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
}