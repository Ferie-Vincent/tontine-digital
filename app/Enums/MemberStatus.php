<?php

namespace App\Enums;

enum MemberStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case EXCLUDED = 'excluded';
    case LEFT = 'left';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'En attente',
            self::ACTIVE => 'Actif',
            self::EXCLUDED => 'Exclu',
            self::LEFT => 'Parti',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::ACTIVE => 'green',
            self::EXCLUDED => 'red',
            self::LEFT => 'gray',
        };
    }
}
