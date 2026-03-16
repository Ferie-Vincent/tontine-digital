<?php

namespace App\Enums;

enum TourStatus: string
{
    case UPCOMING = 'upcoming';
    case ONGOING = 'ongoing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function label(): string
    {
        return match($this) {
            self::UPCOMING => 'À venir',
            self::ONGOING => 'En cours',
            self::COMPLETED => 'Terminé',
            self::FAILED => 'Échoué',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::UPCOMING => 'blue',
            self::ONGOING => 'yellow',
            self::COMPLETED => 'green',
            self::FAILED => 'red',
        };
    }
}
