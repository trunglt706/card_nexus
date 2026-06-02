<?php

namespace App\Services;

use App\Enums\WalletStatus;
use App\Models\User;
use App\Models\Wallet;

class WalletService
{

    /**
     * Create new wallet for user
     *
     * @param User $user
     * @param float $initialBalance
     * @return Wallet
     */
    public function createWalletForUser(User $user, float $initialBalance = 0.00): Wallet
    {
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'balance' => $initialBalance,
            'status' => WalletStatus::ACTIVE,
        ]);

        return $wallet;
    }

    /**
     * Generate checksum for wallet data to ensure integrity
     *
     * @param integer $user_id
     * @param float $balance
     * @return string
     */
    public function generateChecksum(int $user_id, float $balance): string
    {
        // Gom dữ liệu cốt lõi thành một chuỗi văn bản duy nhất
        $rawData = $user_id . '|' . $balance;

        // Lấy mã bí mật của hệ thống làm "chìa khóa khóa"
        $secretKey = config('app.key');

        // Sử dụng thuật toán băm HMAC SHA256 (Chuẩn bảo mật ngân hàng)
        return hash_hmac('sha256', $rawData, $secretKey);
    }

    /**
     * Verify the integrity of wallet data using checksum
     *
     * @param Wallet $wallet
     * @return bool
     */
    public function verifyWalletChecksum(Wallet $wallet): bool
    {
        $expectedChecksum = $this->generateChecksum($wallet->user_id, $wallet->balance);
        return hash_equals($expectedChecksum, $wallet->checksum);
    }

    /**
     * Ban a wallet and update its status and note
     *
     * @param Wallet $wallet
     * @param string $reason
     * @return void
     */
    public function banWallet(Wallet $wallet, string $reason)
    {
        $wallet->update([
            'status' => WalletStatus::BANNED,
            'note' => $reason
        ]);
    }
}
