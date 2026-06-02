<?php

namespace App\Services;

use App\Enums\UserStatus;
use App\Models\User;

class UserService
{
    /**
     * Ban a user by setting their status to BANNED and providing a reason.
     *
     * @param User $user
     * @param string $reason
     * @return void
     */
    public function banUser(User $user, string $reason)
    {
        // 1. Khóa tài khoản User liên quan ngay lập tức
        $user->update([
            'status' => UserStatus::BANNED,
            'note' => $reason
        ]);
    }
}
