<?php

namespace App\Enums;

enum RequestType: string
{
    case WITHDRAWAL = 'withdrawal';
    case DISPUTE = 'dispute';
    case INFO = 'info';
    case PAYMENT = 'payment';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::WITHDRAWAL => 'Demande de retrait',
            self::DISPUTE => 'Contestation',
            self::INFO => 'Demande d\'information',
            self::PAYMENT => 'Probleme de paiement',
            self::OTHER => 'Autre',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::WITHDRAWAL => 'orange',
            self::DISPUTE => 'red',
            self::INFO => 'blue',
            self::PAYMENT => 'yellow',
            self::OTHER => 'gray',
        };
    }
}
