<?php

namespace App\Services;

use App\Enums\UserStatus;
use App\Models\User;

class UserService
{
    public function banUser(User $user, string $reason)
    {
        // 1. Khóa tài khoản User liên quan ngay lập tức
        $user->update([
            'status' => UserStatus::BANNED,
            'note' => $reason
        ]);
    }
}
