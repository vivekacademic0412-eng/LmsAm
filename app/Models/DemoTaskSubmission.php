<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemoTaskSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'demo_task_assignment_id',
        'answer_text',
        'file_path',
        'file_name',
        'file_mime',
        'file_size',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(DemoTaskAssignment::class, 'demo_task_assignment_id');
    }
}
