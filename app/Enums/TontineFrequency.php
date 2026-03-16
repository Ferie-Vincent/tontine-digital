<?php

namespace App\Enums;

enum TontineFrequency: string
{
    case WEEKLY = 'weekly';
    case BIWEEKLY = 'biweekly';
    case MONTHLY = 'monthly';

    public function label(): string
    {
        return match($this) {
            self::WEEKLY => 'Hebdomadaire',
            self::BIWEEKLY => 'Bimensuel',
            self::MONTHLY => 'Mensuel',
        };
    }

    public function days(): int
    {
        return match($this) {
            self::WEEKLY => 7,
            self::BIWEEKLY => 14,
            self::MONTHLY => 30,
        };
    }
}
