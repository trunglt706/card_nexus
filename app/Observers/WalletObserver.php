<?php

namespace App\Observers;

use App\Models\Wallet;

class WalletObserver
{
    // handle the wallet creating event
    public function creating(Wallet $wallet): void
    {
        $wallet->checksum = $wallet->checksum ?? uniqid('WALLET_', true);
        $wallet->balance = $wallet->balance ?? 0.00;
        $wallet->status = $wallet->status ?? 'active';
    }
    /**
     * Handle the Wallet "created" event.
     */
    public function created(Wallet $wallet): void
    {
        //
    }

    /**
     * Handle the Wallet "updated" event.
     */
    public function updated(Wallet $wallet): void
    {
        //
    }

    /**
     * Handle the Wallet "deleted" event.
     */
    public function deleted(Wallet $wallet): void
    {
        //
    }

    /**
     * Handle the Wallet "restored" event.
     */
    public function restored(Wallet $wallet): void
    {
        //
    }

    /**
     * Handle the Wallet "force deleted" event.
     */
    public function forceDeleted(Wallet $wallet): void
    {
        //
    }
}
