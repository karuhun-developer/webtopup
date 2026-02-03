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
        Schema::table('p_p_o_b_brands', function (Blueprint $table) {
            $table->boolean('featured')->default(false)->after('status');
            $table->unsignedInteger('order')->default(0)->after('featured');
            $table->json('settings')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('p_p_o_b_brands', function (Blueprint $table) {
            //
        });
    }
};
