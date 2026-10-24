<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('sp_order_id', 100)->nullable()->index()->after('transaction_id');
            $table->string('payment_channel', 50)->nullable()->after('sp_order_id');
            $table->timestamp('paid_at')->nullable()->after('payment_channel');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['sp_order_id', 'payment_channel', 'paid_at']);
        });
    }
};
