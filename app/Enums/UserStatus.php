<?php

namespace App\Enums;

enum UserStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case BANNED = 'banned';
    case DELETED = 'deleted';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('Pending'),
            self::ACTIVE => __('Active'),
            self::BANNED => __('Banned'),
            self::DELETED => __('Deleted'),
        };
    }
}
