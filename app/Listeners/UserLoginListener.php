<?php

namespace App\Listeners;

use App\Enums\UserGroup;
use App\Events\UserLoginEvent;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UserLoginListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserLoginEvent $event): void
    {
        try {
            $userId = $event->userId;
            $user = User::find($userId);
            if($user) {
                $user->last_login_at = now();
                $user->save();
                $ipAddress = request()->ip();

                Log::info('User logged in: ' . $user->code . ' from IP: ' . $ipAddress . ' with group: ' . $user->group_id->label());
            } else {
                Log::warning('User not found for ID: ' . $userId . ' in UserLoginListener');
            }
        } catch (\Throwable $th) {
            Log::error('Error in UserLoginListener: ' . $th->getMessage());
        }
    }
}
