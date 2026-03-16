<?php

namespace App\Models;

use App\Enums\TourStatus;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    protected $fillable = [
        'tontine_id',
        'beneficiary_id',
        'tour_number',
        'due_date',
        'collection_date',
        'expected_amount',
        'collected_amount',
        'status',
        'notes',
        'disbursed_at',
        'disbursed_by',
        'disbursement_method',
        'disbursement_reference',
        'disbursement_notes',
        'beneficiary_confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'tour_number' => 'integer',
            'due_date' => 'date',
            'collection_date' => 'date',
            'expected_amount' => 'integer',
            'collected_amount' => 'integer',
            'status' => TourStatus::class,
            'disbursed_at' => 'datetime',
            'beneficiary_confirmed_at' => 'datetime',
        ];
    }

    public function tontine()
    {
        return $this->belongsTo(Tontine::class);
    }

    public function beneficiary()
    {
        return $this->belongsTo(User::class, 'beneficiary_id');
    }

    public function disbursedBy()
    {
        return $this->belongsTo(User::class, 'disbursed_by');
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', TourStatus::UPCOMING);
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', TourStatus::ONGOING);
    }

    public function getFormattedExpectedAmountAttribute(): string
    {
        return format_amount($this->expected_amount);
    }

    public function getFormattedCollectedAmountAttribute(): string
    {
        return format_amount($this->collected_amount);
    }

    public function getCollectionProgressAttribute(): float
    {
        if ($this->expected_amount == 0) return 0;
        return round(($this->collected_amount / $this->expected_amount) * 100, 1);
    }

    public function isDisbursed(): bool
    {
        return $this->disbursed_at !== null;
    }

    public function isBeneficiaryConfirmed(): bool
    {
        return $this->beneficiary_confirmed_at !== null;
    }

    public function isFullyPaid(): bool
    {
        $total = $this->contributions()->count();
        if ($total === 0) return false;
        return $this->contributions()->where('status', 'confirmed')->count() === $total;
    }
}
