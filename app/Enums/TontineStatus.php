<?php

namespace App\Enums;

enum TontineStatus: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case PAUSED = 'paused';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Brouillon',
            self::PENDING => 'En attente',
            self::ACTIVE => 'Active',
            self::PAUSED => 'En pause',
            self::COMPLETED => 'Terminée',
            self::CANCELLED => 'Annulée',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::PENDING => 'yellow',
            self::ACTIVE => 'green',
            self::PAUSED => 'amber',
            self::COMPLETED => 'blue',
            self::CANCELLED => 'red',
        };
    }
}
