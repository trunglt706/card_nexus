<?php

namespace App\Observers;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Transaction;

class TransactionObserver
{
    // handle the transaction creating event
    public function creating(Transaction $transaction): void
    {
        $transaction->code = $transaction->code ?? uniqid('TS_', true);
        $transaction->status = $transaction->status ?? TransactionStatus::WAITING;
        $transaction->amount = $transaction->amount ?? 0.00;
        $transaction->type = $transaction->type ?? TransactionType::Deposit;
        $transaction->metadata = $transaction->metadata ?? json_encode(request()->all());
    }

    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        //
    }
}
