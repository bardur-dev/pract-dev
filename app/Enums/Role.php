<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case USER = 'user';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Администратор',
            self::USER => 'Пользователь',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
