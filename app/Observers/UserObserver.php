<?php

namespace App\Observers;

use App\Enums\UserGroup;
use App\Enums\UserStatus;
use App\Enums\WalletStatus;
use App\Models\User;

class UserObserver
{
    // handle the user creating event
    public function creating(User $user): void
    {
        $user->code = $user->code ?? uniqid('U_', true);
        $user->status = $user->status ?? UserStatus::PENDING;
        $user->group_id = $user->group_id ?? UserGroup::REGULAR;
        $user->password = $user->password ?? bcrypt(uniqid('P_', true));
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // send notify to user via email to active account
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    // handle the user deleting event
    public function deleting(User $user): void
    {
        $user->status = UserStatus::DELETED;
        $user->email = 'deleted-' . $user->email;

        // close wallet if exists
        if ($user->wallet) {
            $user->wallet->status = WalletStatus::CLOSED;
            $user->wallet->note = 'Đóng ví do tài khoản bị xóa';
            $user->wallet->save();
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // send notify to user via email to inform account is deleted
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
