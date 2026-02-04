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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(App\Models\User::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(App\Models\PPOB\PPOBBrand::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(App\Models\PPOB\PPOBProduct::class)->nullable()->constrained()->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->unsignedBigInteger('ref_number')->unique();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->json('submited')->nullable();
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('fee');
            $table->unsignedBigInteger('total_amount');
            $table->boolean('payment_status')->default(false);
            $table->boolean('topup_status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
