<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case WAITING = 'waiting';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case EXPIRED = 'expired';
    case CANCELED = 'canceled';

    public function label(): string
    {
        return match ($this) {
            self::WAITING => __('Waiting'),
            self::SUCCESS => __('Success'),
            self::FAILED => __('Failed'),
            self::EXPIRED => __('Expired'),
            self::CANCELED => __('Canceled'),
        };
    }
}
