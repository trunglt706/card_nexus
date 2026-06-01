<?php

namespace App\Enums;

enum WalletStatus: string
{
    case ACTIVE = 'active';
    case BANNED = 'banned';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => __('Active'),
            self::BANNED => __('Banned'),
            self::CLOSED => __('Closed'),
        };
    }
}
