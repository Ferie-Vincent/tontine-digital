<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'device_name',
        'last_activity',
    ];

    protected function casts(): array
    {
        return [
            'last_activity' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Parse un user agent pour extraire un nom d'appareil lisible.
     */
    public static function parseDeviceName(?string $userAgent): string
    {
        if (empty($userAgent)) {
            return 'Appareil inconnu';
        }

        // Detecter le navigateur
        $browser = 'Navigateur inconnu';
        if (str_contains($userAgent, 'Edg/') || str_contains($userAgent, 'Edge/')) {
            $browser = 'Edge';
        } elseif (str_contains($userAgent, 'OPR/') || str_contains($userAgent, 'Opera')) {
            $browser = 'Opera';
        } elseif (str_contains($userAgent, 'Chrome/') && !str_contains($userAgent, 'Chromium/')) {
            $browser = 'Chrome';
        } elseif (str_contains($userAgent, 'Firefox/')) {
            $browser = 'Firefox';
        } elseif (str_contains($userAgent, 'Safari/') && !str_contains($userAgent, 'Chrome/')) {
            $browser = 'Safari';
        } elseif (str_contains($userAgent, 'MSIE') || str_contains($userAgent, 'Trident/')) {
            $browser = 'Internet Explorer';
        }

        // Detecter le systeme d'exploitation
        $os = 'OS inconnu';
        if (str_contains($userAgent, 'iPhone')) {
            $os = 'iPhone';
        } elseif (str_contains($userAgent, 'iPad')) {
            $os = 'iPad';
        } elseif (str_contains($userAgent, 'Android')) {
            $os = 'Android';
        } elseif (str_contains($userAgent, 'Windows')) {
            $os = 'Windows';
        } elseif (str_contains($userAgent, 'Macintosh') || str_contains($userAgent, 'Mac OS')) {
            $os = 'Mac';
        } elseif (str_contains($userAgent, 'Linux')) {
            $os = 'Linux';
        } elseif (str_contains($userAgent, 'CrOS')) {
            $os = 'Chrome OS';
        }

        return "{$browser} sur {$os}";
    }

    /**
     * Determine si c'est un appareil mobile.
     */
    public function isMobile(): bool
    {
        if (empty($this->user_agent)) {
            return false;
        }

        return str_contains($this->user_agent, 'Mobile')
            || str_contains($this->user_agent, 'Android')
            || str_contains($this->user_agent, 'iPhone')
            || str_contains($this->user_agent, 'iPad');
    }
}
