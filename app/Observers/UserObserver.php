<?php

namespace App\Observers;

use App\Enums\UserGroup;
use App\Enums\UserStatus;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

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
        if($user->isDirty('status') && $user->status === UserStatus::BANNED) {
            // send notify to user via email to inform account is banned
            // ban all wallets of user
            $walletService = new WalletService();
            foreach ($user->wallets as $wallet) {
                $walletService->banWallet($wallet, $user->note ?? 'User account is banned');
            }

            // 2. Kích hoạt lệnh xóa phiên đăng nhập trên Redis để đá User khỏi hệ thống
            Redis::del("user_session:{$user->id}");

            // 3. Bắn cảnh báo khẩn cấp về Chatwork / Telegram cho Đội Kỹ thuật
            Log::alert("CẢNH BÁO: Phát hiện gian lận số dư tại User ID: {$user->id}. Ví đã bị khóa tự động.");
        }
    }

    // handle the user deleting event
    public function deleting(User $user): void
    {
        //
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
