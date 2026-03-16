<?php

namespace App\Models;

use App\Enums\MemberRole;
use App\Enums\MemberStatus;
use Illuminate\Database\Eloquent\Model;

class TontineMember extends Model
{
    protected $fillable = [
        'tontine_id',
        'user_id',
        'role',
        'position',
        'parts',
        'status',
        'joined_at',
    ];

    protected function casts(): array
    {
        return [
            'role' => MemberRole::class,
            'status' => MemberStatus::class,
            'joined_at' => 'datetime',
            'position' => 'integer',
            'parts' => 'integer',
        ];
    }

    public function tontine()
    {
        return $this->belongsTo(Tontine::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', MemberStatus::ACTIVE);
    }

    public function scopePending($query)
    {
        return $query->where('status', MemberStatus::PENDING);
    }

    public function isAdmin(): bool
    {
        return $this->role === MemberRole::ADMIN;
    }

    public function isTreasurer(): bool
    {
        return $this->role === MemberRole::TREASURER;
    }

    public function canManage(): bool
    {
        return in_array($this->role, [MemberRole::ADMIN, MemberRole::TREASURER]);
    }

    public function getContributionPerTourAttribute(): int
    {
        return $this->parts * $this->tontine->contribution_amount;
    }
}
