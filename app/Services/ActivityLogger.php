<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ActivityLogger
{
    /**
     * @param  array<string, mixed>  $context
     */
    public static function log(?User $actor, string $module, string $action, string $description, array $context = []): void
    {
        if (! Schema::hasTable('activity_logs')) {
            return;
        }

        try {
            $properties = is_array($context['properties'] ?? null) ? $context['properties'] : [];
            $actorSnapshot = array_filter([
                'name' => $actor?->name,
                'email' => $actor?->email,
                'role' => $actor?->role,
            ], fn ($value) => filled($value));

            if ($actorSnapshot !== []) {
                $properties['_actor'] = $actorSnapshot;
            }

            ActivityLog::create([
                'user_id' => $actor?->id,
                'module' => $module,
                'action' => $action,
                'description' => $description,
                'subject_type' => $context['subject_type'] ?? null,
                'subject_id' => isset($context['subject_id']) ? (string) $context['subject_id'] : null,
                'subject_label' => $context['subject_label'] ?? null,
                'route_name' => $context['route_name'] ?? null,
                'method' => $context['method'] ?? 'SYSTEM',
                'url' => $context['url'] ?? null,
                'ip_address' => $context['ip_address'] ?? null,
                'user_agent' => $context['user_agent'] ?? null,
                'properties' => $properties !== [] ? $properties : null,
            ]);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
