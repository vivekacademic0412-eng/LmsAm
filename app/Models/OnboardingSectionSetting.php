<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnboardingSectionSetting extends Model
{
    use HasFactory;

    protected $fillable = ['role', 'section_key', 'editable'];

    protected $casts = ['editable' => 'boolean'];

    /**
     * Is $role allowed to edit $sectionKey after onboarding is completed?
     * Defaults to true (editable) when no explicit setting row exists yet,
     * so nothing silently locks itself before an admin configures it.
     */
    public static function isEditable(string $role, string $sectionKey): bool
    {
        return static::query()
            ->where('role', $role)
            ->where('section_key', $sectionKey)
            ->value('editable') ?? true;
    }
}