<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('p_p_o_b_products', function (Blueprint $table) {
            $table->string('provider_product_id')->nullable();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->string('provider')->nullable();
            $table->string('provider_order_id')->nullable()->index();
            $table->json('provider_payload')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['provider', 'provider_order_id', 'provider_payload']);
        });
        Schema::table('p_p_o_b_products', function (Blueprint $table) {
            $table->dropColumn('provider_product_id');
        });
    }
};
