<?php

namespace App\Enums;

enum TransactionType: string
{
    case DEPOSIT = 'deposit';
    case WITHDRAW = 'withdraw';
    case TRANSFER = 'transfer';
    case REFUND = 'refund';
    case BONUS = 'bonus';
    case BY_CARD = 'by_card';

    public function label(): string
    {
        return match ($this) {
            self::DEPOSIT => __('Deposit'),
            self::WITHDRAW => __('Withdraw'),
            self::TRANSFER => __('Transfer'),
            self::REFUND => __('Refund'),
            self::BONUS => __('Bonus'),
            self::BY_CARD => __('By Card'),
        };
    }
}
