<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Brochure extends Model
{
    protected $fillable = [
        'title',
        'file_path',
        'original_name',
        'file_size',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'file_size'  => 'integer',
        'sort_order' => 'integer',
    ];

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size ?? 0;

        if ($bytes <= 0) {
            return '—';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $i = (int) floor(log($bytes, 1024));
        $i = min($i, count($units) - 1);

        return round($bytes / (1024 ** $i), $i === 0 ? 0 : 1) . ' ' . $units[$i];
    }

    /**
     * Convenience helper for the public site, e.g. to render the
     * currently active brochure download link.
     */
    public static function active()
    {
        return static::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }
}