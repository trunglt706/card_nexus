<?php

namespace App\Enums;

enum LedgerEntryType: string
{
    case DEPOSIT = 'deposit';
    case WITHDRAW = 'withdraw';
    case TRANSFER = 'transfer';

    public function label(): string
    {
        return match ($this) {
            self::DEPOSIT => __('Deposit'),
            self::WITHDRAW => __('Withdraw'),
            self::TRANSFER => __('Transfer'),
        };
    }
}
