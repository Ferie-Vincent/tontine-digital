<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case ORANGE_MONEY = 'orange_money';
    case MTN_MOMO = 'mtn_momo';
    case WAVE = 'wave';
    case CASH = 'cash';
    case BANK_TRANSFER = 'bank_transfer';
    case MOOV_MONEY = 'moov_money';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::ORANGE_MONEY => 'Orange Money',
            self::MTN_MOMO => 'MTN MoMo',
            self::WAVE => 'Wave',
            self::CASH => 'Espèces',
            self::BANK_TRANSFER => 'Virement bancaire',
            self::MOOV_MONEY => 'Moov Money',
            self::OTHER => 'Autre',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ORANGE_MONEY => 'orange',
            self::MTN_MOMO => 'yellow',
            self::WAVE => 'blue',
            self::CASH => 'green',
            self::BANK_TRANSFER => 'indigo',
            self::MOOV_MONEY => 'cyan',
            self::OTHER => 'gray',
        };
    }
}
