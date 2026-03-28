<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemoFeatureVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'position',
        'title',
        'description',
        'file_path',
        'file_name',
        'file_mime',
        'file_size',
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
}
