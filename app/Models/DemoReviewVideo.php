<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemoReviewVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'position',
        'title',
        'description',
        'youtube_url',
        'youtube_id',
        'uploaded_by',
    ];

    protected $casts = [
        'position' => 'integer',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getEmbedUrlAttribute(): ?string
    {
        if (! $this->youtube_id) {
            return null;
        }

        return 'https://www.youtube-nocookie.com/embed/'.$this->youtube_id.'?rel=0';
    }

    public function getWatchUrlAttribute(): ?string
    {
        if (! $this->youtube_id) {
            return null;
        }

        return 'https://www.youtube.com/watch?v='.$this->youtube_id;
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if (! $this->youtube_id) {
            return null;
        }

        return 'https://i.ytimg.com/vi/'.$this->youtube_id.'/hqdefault.jpg';
    }
}
