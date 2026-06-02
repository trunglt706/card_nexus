<?php

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->nullable();
            $table->foreignId('wallet_id')->constrained('wallets')->onDelete('restrict')->nullable();
            $table->decimal('amount', 15, 2); // Dương là IN, Âm là OUT
            $table->string('type')->default('deposit'); // 'deposit', 'withdraw', 'transfer'
            $table->string('reference_id')->nullable(); // Mã đơn hàng hoặc mã giao dịch đối tác
            $table->string('status')->default('waiting'); // 'waiting', 'success', 'failed', 'expired'
            $table->string('description')->nullable();
            $table->json('metadata')->nullable(); // Lưu trữ thông tin bổ sung dưới dạng JSON
            $table->timestamps();
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
