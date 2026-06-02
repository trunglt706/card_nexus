<?php

use App\Enums\LedgerEntryType;
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
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('wallets')->onDelete('restrict');
            $table->decimal('amount', 15, 2); // Dương là IN, Âm là OUT
            $table->string('type')->default('deposit'); // 'deposit', 'withdraw', 'transfer'
            $table->string('reference_id')->nullable(); // Mã đơn hàng hoặc mã giao dịch đối tác
            $table->timestamps();

            // Index hỗn hợp để tối ưu hóa truy vấn tính toán
            $table->index(['wallet_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
    }
};
