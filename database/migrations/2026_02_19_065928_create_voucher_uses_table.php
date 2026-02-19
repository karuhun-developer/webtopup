<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('voucher_uses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Voucher\Voucher::class)->constrained()->cascadeOnDelete();
            $table->morphs('usable');
            $table->unsignedBigInteger('before_amount');
            $table->unsignedBigInteger('discount_amount');
            $table->unsignedBigInteger('after_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_uses');
    }
};
