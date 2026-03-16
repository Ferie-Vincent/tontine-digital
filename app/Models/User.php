<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'must_change_password',
        'avatar',
        'status',
        'is_admin',
        'phone_verified_at',
        'notification_digest',
        'locked_until',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
            'is_admin' => 'boolean',
            'locked_until' => 'datetime',
        ];
    }

    // Relations

    public function createdTontines()
    {
        return $this->hasMany(Tontine::class, 'creator_id');
    }

    public function tontineMembers()
    {
        return $this->hasMany(TontineMember::class);
    }

    public function tontines()
    {
        return $this->belongsToMany(Tontine::class, 'tontine_members')
            ->withPivot('role', 'position', 'status', 'joined_at')
            ->withTimestamps();
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }

    public function tours()
    {
        return $this->hasMany(Tour::class, 'beneficiary_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function userNotifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function pushSubscriptions()
    {
        return $this->hasMany(PushSubscription::class);
    }

    public function sessions()
    {
        return $this->hasMany(UserSession::class);
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Accessors

    public function getFormattedPhoneAttribute(): string
    {
        $phone = $this->phone;
        if (str_starts_with($phone, '+225')) {
            $phone = substr($phone, 4);
        }
        if (strlen($phone) >= 10) {
            return '+225 ' . substr($phone, 0, 2) . ' ' . substr($phone, 2, 2) . ' ' . substr($phone, 4, 2) . ' ' . substr($phone, 6, 2) . ' ' . substr($phone, 8);
        }
        return $this->phone;
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return $this->generateLocalAvatarUrl();
    }

    private function generateLocalAvatarUrl(): string
    {
        return static::generateAvatarSvg($this->name);
    }

    public static function generateAvatarSvg(?string $name): string
    {
        $initials = collect(explode(' ', $name ?? ''))
            ->filter()
            ->map(fn($word) => mb_strtoupper(mb_substr($word, 0, 1)))
            ->take(2)
            ->implode('');

        if (empty($initials)) {
            $initials = '?';
        }

        $colors = ['3C50E0','2E86AB','E04C3C','E0A03C','50E063','9B59B6','1ABC9C','E67E22','34495E','16A085'];
        $index = crc32($name ?? '') % count($colors);
        $bg = $colors[abs($index)];

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" viewBox="0 0 128 128">'
             . '<rect width="128" height="128" rx="64" fill="#' . $bg . '"/>'
             . '<text x="64" y="64" dy=".35em" text-anchor="middle" fill="white" font-family="Arial,sans-serif" font-size="48" font-weight="bold">'
             . htmlspecialchars($initials)
             . '</text></svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    // Helpers

    public function isPhoneVerified(): bool
    {
        return $this->phone_verified_at !== null;
    }

    public function isMemberOf(Tontine $tontine): bool
    {
        return $this->tontineMembers()
            ->where('tontine_id', $tontine->id)
            ->whereIn('status', ['active', 'pending'])
            ->exists();
    }

    public function roleIn(Tontine $tontine): ?string
    {
        $member = $this->tontineMembers()
            ->where('tontine_id', $tontine->id)
            ->first();
        return $member?->role?->value;
    }

    public function isAdminOf(Tontine $tontine): bool
    {
        return $this->roleIn($tontine) === 'admin';
    }

    public function isTreasurerOf(Tontine $tontine): bool
    {
        return $this->roleIn($tontine) === 'treasurer';
    }

    public function canManage(Tontine $tontine): bool
    {
        return in_array($this->roleIn($tontine), ['admin', 'treasurer']);
    }
}
