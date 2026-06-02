<?php

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
        Schema::table('users', function (Blueprint $table) {
            $table->string('code')->unique()->after('id')->nullable();
            $table->string('avatar')->nullable();
            $table->unsignedTinyInteger('group_id')->default(1); // 1: Mặc định, 2: Vip 1, 3: Đại lý...
            $table->string('status', 30)->default('pending'); // pending, active, banned
            $table->string('phone')->nullable();
            $table->string('note')->nullable();
            $table->timestamp('last_login_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'group_id', 'status', 'phone', 'note', 'last_login_at']);
        });
    }
};
