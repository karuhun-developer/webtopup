<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('p_p_o_b_products', function (Blueprint $table) {
            $table->dropUnique(['sku']);
            $table->unique(['provider', 'sku'], 'products_provider_sku_unique');
        });
    }

    public function down(): void
    {
        Schema::table('p_p_o_b_products', function (Blueprint $table) {
            $table->unique('sku');
            $table->dropUnique('products_provider_sku_unique');
        });
    }
};
