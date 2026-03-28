<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DemoTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'resource_url',
        'resource_file_path',
        'resource_file_name',
        'resource_file_mime',
        'resource_file_size',
        'task_video_path',
        'task_video_name',
        'task_video_mime',
        'task_video_size',
        'ai_video_url',
        'created_by',
    ];

    protected $casts = [
        'resource_file_size' => 'integer',
        'task_video_size' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(DemoTaskAssignment::class, 'demo_task_id');
    }
}
