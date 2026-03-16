<?php

namespace App\Enums;

enum MemberRole: string
{
    case ADMIN = 'admin';
    case TREASURER = 'treasurer';
    case MEMBER = 'member';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrateur',
            self::TREASURER => 'Trésorier',
            self::MEMBER => 'Membre',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ADMIN => 'purple',
            self::TREASURER => 'blue',
            self::MEMBER => 'gray',
        };
    }
}
