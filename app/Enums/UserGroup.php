<?php

namespace App\Enums;

enum UserGroup: int
{
    case VIP = 1;
    case REGULAR = 2;
    case STAFF = 3;
    case SUPPLIER = 4;

    public function label(): string
    {
        return match ($this) {
            self::VIP => __('VIP'),
            self::REGULAR => __('Regular'),
            self::STAFF => __('Staff'),
            self::SUPPLIER => __('Supplier')
        };
    }
}
