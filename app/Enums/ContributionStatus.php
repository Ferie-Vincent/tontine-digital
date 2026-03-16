<?php

namespace App\Enums;

enum ContributionStatus: string
{
    case PENDING = 'pending';
    case DECLARED = 'declared';
    case CONFIRMED = 'confirmed';
    case REJECTED = 'rejected';
    case LATE = 'late';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'En attente',
            self::DECLARED => 'Déclaré',
            self::CONFIRMED => 'Confirmé',
            self::REJECTED => 'Rejeté',
            self::LATE => 'En retard',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'gray',
            self::DECLARED => 'yellow',
            self::CONFIRMED => 'green',
            self::REJECTED => 'red',
            self::LATE => 'orange',
        };
    }
}
