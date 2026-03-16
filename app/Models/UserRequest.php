<?php

namespace App\Models;

use App\Enums\RequestStatus;
use App\Enums\RequestType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class UserRequest extends Model
{
    protected $fillable = [
        'user_id',
        'tontine_id',
        'type',
        'subject',
        'description',
        'status',
        'priority',
        'admin_response',
        'responded_by',
        'responded_at',
    ];

    protected $casts = [
        'type' => RequestType::class,
        'status' => RequestStatus::class,
        'responded_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tontine(): BelongsTo
    {
        return $this->belongsTo(Tontine::class);
    }

    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', RequestStatus::PENDING);
    }

    public function scopeForTontine(Builder $query, int $tontineId): Builder
    {
        return $query->where('tontine_id', $tontineId);
    }
}
