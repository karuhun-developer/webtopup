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
        Schema::create('p_p_o_b_deposits', function (Blueprint $table) {
            $table->id();
            $table->string('bank');
            $table->string('payment_method');
            $table->string('owner_name');
            $table->string('account_number')->nullable();
            $table->unsignedBigInteger('amount');
            $table->string('notes')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p_p_o_b_deposits');
    }
};
