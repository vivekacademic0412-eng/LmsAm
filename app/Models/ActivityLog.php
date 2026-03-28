<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'module',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'subject_label',
        'route_name',
        'method',
        'url',
        'ip_address',
        'user_agent',
        'properties',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actorName(): string
    {
        return $this->user?->name
            ?? data_get($this->properties, '_actor.name')
            ?? 'System';
    }

    public function actorEmail(): ?string
    {
        return $this->user?->email
            ?? data_get($this->properties, '_actor.email');
    }

    public function actorRoleLabel(): ?string
    {
        $role = $this->user?->role ?? data_get($this->properties, '_actor.role');

        return $role ? (User::roleOptions()[$role] ?? Str::headline((string) $role)) : null;
    }

    public function actionLabel(): string
    {
        return Str::headline((string) $this->action);
    }

    public function actionTone(): string
    {
        return match ($this->action) {
            'login' => 'success',
            'logout' => 'muted',
            'blocked_login' => 'warning',
            'delete' => 'danger',
            'submit' => 'warning',
            'review' => 'accent',
            'send', 'assign', 'toggle_live', 'create' => 'accent',
            default => 'default',
        };
    }

    public function payloadForDisplay(): ?array
    {
        $payload = $this->properties ?? [];

        unset($payload['_actor']);

        return $payload !== [] ? $payload : null;
    }
}
