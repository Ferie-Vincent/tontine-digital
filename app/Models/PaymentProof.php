<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Model;

class PaymentProof extends Model
{
    protected $fillable = [
        'contribution_id',
        'transaction_reference',
        'payment_method',
        'sender_phone',
        'receiver_phone',
        'screenshot_path',
        'transaction_date',
        'verification_status',
        'rejection_reason',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'payment_method' => PaymentMethod::class,
            'transaction_date' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function contribution()
    {
        return $this->belongsTo(Contribution::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getScreenshotUrlAttribute(): ?string
    {
        if ($this->screenshot_path) {
            return asset('storage/' . $this->screenshot_path);
        }
        return null;
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        if ($this->payment_method instanceof PaymentMethod) {
            return $this->payment_method->label();
        }

        // Fallback pour les valeurs non castées
        return ucfirst(str_replace('_', ' ', $this->payment_method ?? ''));
    }
}
