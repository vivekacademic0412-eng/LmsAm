<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemoFeatureVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'position',
        'title',
        'description',
        'file_path',
        'file_name',
        'file_mime',
        'file_size',
        'status',
        'uploaded_by',
    ];

    protected $casts = [
        'position' => 'integer',
        'file_size' => 'integer',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
     public function category(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }
    
}
