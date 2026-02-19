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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->comment('FIXED_AMOUNT, PERCENTAGE');
            $table->unsignedBigInteger('fixed_amount')->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->unsignedBigInteger('min_purchase_amount')->comment('0 means no minimum');
            $table->unsignedBigInteger('usage_limit')->comment('0 means unlimited');
            $table->unsignedBigInteger('used_count')->default(0);
            $table->boolean('status')->default(true)->comment('1 for active, 0 for inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
