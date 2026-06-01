<?php

namespace App\Console\Commands;

use App\Enums\UserStatus;
use App\Enums\WalletStatus;
use App\Models\Wallet;
use App\Services\UserService;
use App\Services\WalletService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class AuditWallets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:audit-wallets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit wallets for consistency and integrity';

    /**
     * Execute the console command.
     */
    public function handle(WalletService $walletService, UserService $userService)
    {
        try {
            $this->info('--- BẮT ĐẦU TIẾN TRÌNH ĐỐI SOÁT TÀI KHOẢN ---');
            $wallets = Wallet::whereStatus(WalletStatus::ACTIVE)->get();
            $discrepancyCount = 0;

            foreach ($wallets as $wallet) {
                if (!$walletService->verifyWalletChecksum($wallet)) {
                    $reason = 'Tài khoản bị khóa do phát hiện gian lận số dư ví.';
                    // ban wallet và user liên quan ngay lập tức nếu phát hiện gian lận
                    $walletService->banWallet($wallet, $reason);
                    $userService->banUser($wallet->user, $reason);

                    $message = "🚨 PHÁT HIỆN CHECKSUM KHÔNG HỢP LỆ WALLET ID: {$wallet->id}!";

                    $this->error($message);
                    Log::emergency($message);

                    $discrepancyCount++;
                }
            }

            $this->info("--- KẾT THÚC ĐỐI SOÁT. Phát hiện {$discrepancyCount} tài khoản lỗi/bị hack. ---");
            Log::info("KẾT THÚC ĐỐI SOÁT. Phát hiện {$discrepancyCount} tài khoản lỗi/bị hack.");
            return command::SUCCESS;
        } catch (\Throwable $th) {
            Log::error("Lỗi khi thực thi lệnh app:audit-wallets: " . $th->getMessage());
            $this->error("Đã xảy ra lỗi khi kiểm tra ví. Vui lòng kiểm tra log để biết chi tiết.");
            return command::FAILURE;
        }
    }
}
