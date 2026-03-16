<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'phone',
        'ip_address',
        'attempted_at',
    ];

    protected function casts(): array
    {
        return [
            'attempted_at' => 'datetime',
        ];
    }

    /**
     * Compter les tentatives echouees pour un numero dans les X dernieres minutes.
     */
    public static function countRecent(string $phone, int $minutes): int
    {
        return self::where('phone', $phone)
            ->where('attempted_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    /**
     * Enregistrer une tentative echouee.
     */
    public static function record(string $phone): self
    {
        return self::create([
            'phone' => $phone,
            'ip_address' => request()->ip(),
            'attempted_at' => now(),
        ]);
    }

    /**
     * Supprimer les tentatives pour un numero (apres connexion reussie).
     */
    public static function clearFor(string $phone): void
    {
        self::where('phone', $phone)->delete();
    }

    /**
     * Nettoyer les anciennes entrees (plus de 24h).
     */
    public static function cleanup(): int
    {
        return self::where('attempted_at', '<', now()->subHours(24))->delete();
    }
}
