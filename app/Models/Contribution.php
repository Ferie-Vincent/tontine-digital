<?php

namespace App\Models;

use App\Enums\ContributionStatus;
use Illuminate\Database\Eloquent\Model;

class Contribution extends Model
{
    protected $fillable = [
        'tour_id',
        'user_id',
        'tontine_id',
        'amount',
        'penalty_amount',
        'status',
        'declared_at',
        'confirmed_at',
        'confirmed_by',
        'notes',
        'requires_review',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'penalty_amount' => 'integer',
            'status' => ContributionStatus::class,
            'declared_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'requires_review' => 'boolean',
        ];
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tontine()
    {
        return $this->belongsTo(Tontine::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function paymentProof()
    {
        return $this->hasOne(PaymentProof::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', ContributionStatus::PENDING);
    }

    public function scopeDeclared($query)
    {
        return $query->where('status', ContributionStatus::DECLARED);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', ContributionStatus::CONFIRMED);
    }

    public function getFormattedAmountAttribute(): string
    {
        return format_amount($this->amount);
    }
}
