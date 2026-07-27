<?php
// app/Models/TrafficSource.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class TrafficSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_ip',
        'session_id',
        'source',
        'referrer_url',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'landing_page',
        'device',
        'browser',
        'platform',
        'user_agent',
        'demo_user_id',
    ];

    /**
     * Recognized source buckets. Anything outside this list still gets
     * stored as-is (e.g. an unexpected utm_source value) — this is just
     * used for normalizing the dashboard chart labels/colors.
     */
    public const KNOWN_SOURCES = [
        'facebook'  => ['label' => 'Facebook',  'color' => '#1877F2'],
        'google'    => ['label' => 'Google',    'color' => '#EA4335'],
        'linkedin'  => ['label' => 'LinkedIn',  'color' => '#0A66C2'],
        'youtube'   => ['label' => 'YouTube',   'color' => '#FF0000'],
        'instagram' => ['label' => 'Instagram', 'color' => '#E1306C'],
        'whatsapp'  => ['label' => 'WhatsApp',  'color' => '#25D366'],
        'direct'    => ['label' => 'Direct',    'color' => '#5a718a'],
    ];

    public function demoUser()
    {
        return $this->belongsTo(DemoUser::class);
    }

    public function demoTypeSelections()
    {
        return $this->hasMany(DemoTypeSelection::class);
    }

    /**
     * Pretty label for dashboard display. Falls back to a humanized
     * version of the raw source string (e.g. "partner-site" → "Partner Site")
     * for anything not in KNOWN_SOURCES.
     */
    public function getSourceLabelAttribute(): string
    {
        $key = strtolower((string) $this->source);

        if (isset(self::KNOWN_SOURCES[$key])) {
            return self::KNOWN_SOURCES[$key]['label'];
        }

        if (blank($this->source)) {
            return 'Direct';
        }

        return str(str_replace(['-', '_'], ' ', $this->source))->title();
    }

    public function getSourceColorAttribute(): string
    {
        $key = strtolower((string) $this->source);
        return self::KNOWN_SOURCES[$key]['color'] ?? '#7a5cff';
    }

    /**
     * Build a TrafficSource attributes array from a browser-facing request
     * (session available, query-string driven — e.g. the landing page
     * itself). Kept for any entry point that still needs query-string
     * semantics specifically.
     */
    public static function attributesFromRequest(\Illuminate\Http\Request $request): array
    {
        $ua = $request->userAgent() ?? '';

        return [
            'user_ip'      => $request->ip(),
            'session_id'   => $request->hasSession()
                ? $request->session()->getId()
                : null,
            'source'       => self::resolveSource($request),
            'referrer_url' => $request->headers->get('referer'),
            'utm_source'   => $request->query('utm_source'),
            'utm_medium'   => $request->query('utm_medium'),
            'utm_campaign' => $request->query('utm_campaign'),
            'utm_term'     => $request->query('utm_term'),
            'utm_content'  => $request->query('utm_content'),
            'landing_page' => $request->fullUrl(),
            'device'       => self::detectDevice($ua),
            'browser'      => self::detectBrowser($ua),
            'platform'     => self::detectPlatform($ua),
            'user_agent'   => $ua,
        ];
    }

    /**
     * Build a TrafficSource attributes array from a server-to-server API
     * request (JSON body, e.g. the Livewire form posting to the LMS API).
     * Uses input() everywhere instead of query() — the previous bug here
     * was resolveSource() calling ->query() on a JSON POST body, which
     * always returned null and threw on strtolower(null), silently
     * rolling back the whole registration transaction.
     */
    public static function attributesFromRequestNew(\Illuminate\Http\Request $request): array
    {
        $ua = $request->userAgent() ?? '';

        $attributes = [
            'user_ip'      => $request->ip(),
            'source'       => self::resolveSource($request),
            'utm_source'   => $request->input('utm_source'),
            'utm_medium'   => $request->input('utm_medium'),
            'utm_campaign' => $request->input('utm_campaign'),
            'utm_term'     => $request->input('utm_term'),
            'utm_content'  => $request->input('utm_content'),
            'landing_page' => $request->fullUrl(),
            'referrer_url' => $request->headers->get('referer'),
            'device'       => self::detectDevice($ua),
            'browser'      => self::detectBrowser($ua),
            'platform'     => self::detectPlatform($ua),
            'user_agent'   => $ua,
        ];

        Log::info('TrafficSource::attributesFromRequestNew built', $attributes);

        return $attributes;
    }

    /**
     * Source resolution priority:
     *   1. Explicit source param (your partner links / API caller use this)
     *   2. utm_source param
     *   3. Parsed from the HTTP referrer domain (facebook.com → facebook)
     *   4. 'direct' if none of the above
     *
     * Uses input() (query string + POST body + JSON body) rather than
     * query() (query string only) so this works correctly whether it's
     * called from a browser GET or a server-to-server JSON POST.
     */
    protected static function resolveSource(\Illuminate\Http\Request $request): string
    {
        if ($request->filled('source')) {
            return strtolower((string) $request->input('source'));
        }

        if ($request->filled('utm_source')) {
            return strtolower((string) $request->input('utm_source'));
        }

        $referrer = $request->headers->get('referer');
        if ($referrer) {
            $host = strtolower((string) parse_url($referrer, PHP_URL_HOST));
            foreach (['facebook', 'google', 'linkedin', 'youtube', 'instagram', 'whatsapp'] as $known) {
                if (str_contains($host, $known)) {
                    return $known;
                }
            }
            if ($host && !str_contains($host, $request->getHost())) {
                return $host;
            }
        }

        return 'direct';
    }

    protected static function detectDevice(string $ua): string
    {
        if (preg_match('/tablet|ipad/i', $ua)) return 'tablet';
        if (preg_match('/mobile|android|iphone/i', $ua)) return 'mobile';
        return 'desktop';
    }

    protected static function detectBrowser(string $ua): string
    {
        return match (true) {
            str_contains($ua, 'Edg/')     => 'Edge',
            str_contains($ua, 'Chrome/')  => 'Chrome',
            str_contains($ua, 'Firefox/') => 'Firefox',
            str_contains($ua, 'Safari/')  => 'Safari',
            str_contains($ua, 'OPR/')     => 'Opera',
            default                       => 'Other',
        };
    }

    protected static function detectPlatform(string $ua): string
    {
        return match (true) {
            str_contains($ua, 'Windows') => 'Windows',
            str_contains($ua, 'Mac OS')  => 'macOS',
            str_contains($ua, 'Android') => 'Android',
            str_contains($ua, 'iPhone')
                || str_contains($ua, 'iPad') => 'iOS',
            str_contains($ua, 'Linux')   => 'Linux',
            default                      => 'Other',
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'demo_user_id');
    }

    /**
     * Human readable label for "how the user came" — prefers campaign,
     * falls back to utm_source, then raw source, then Direct.
     */
    public function getAcquisitionLabelAttribute(): string
    {
        if ($this->utm_campaign) {
            return $this->utm_campaign . ($this->utm_source ? " ({$this->utm_source})" : '');
        }
        if ($this->utm_source) {
            return $this->utm_source;
        }
        return $this->source ?: 'Direct / Unknown';
    }
}