<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->text('gateway_token')->nullable();
            $table->text('gateway_payload')->nullable();
            $table->text('payment_url')->nullable();
            $table->unsignedInteger('unique_amount')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['gateway_token', 'gateway_payload', 'payment_url', 'unique_amount']);
        });
    }
};
