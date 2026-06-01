<?php

namespace App\Models;

use App\Enums\WalletStatus;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['user_id', 'balance', 'checksum', 'status', 'note'];

    protected $casts = [
        'balance' => 'decimal:2',
        'user_id' => 'integer',
        'status' => WalletStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
