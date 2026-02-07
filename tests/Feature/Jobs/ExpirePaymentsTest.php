<?php

use App\Enums\PaymentStatusEnum;
use App\Jobs\ExpirePayments;
use App\Models\Order\Order;
use App\Models\Payment\Payment;
use App\Models\PPOB\PPOBBrand;
use App\Models\PPOB\PPOBCategory;
use App\Models\User;

test('expired payments with pending status are updated to expired', function () {
    $user = User::factory()->create();

    $category = PPOBCategory::create([
        'name' => 'Test Category',
        'slug' => 'test-category-'.uniqid(),
    ]);

    $brand = PPOBBrand::create([
        'p_p_o_b_category_id' => $category->id,
        'name' => 'Test Brand',
        'slug' => 'test-brand-'.uniqid(),
    ]);

    $order = Order::create([
        'user_id' => $user->id,
        'p_p_o_b_brand_id' => $brand->id,
        'reference' => 'TEST-'.uniqid(),
        'ref_number' => rand(1000000000, 9999999999),
        'name' => 'Test Order',
        'email' => 'test@example.com',
        'phone' => '08123456789',
        'amount' => 10000,
        'fee' => 1000,
        'total_amount' => 11000,
        'payment_status' => PaymentStatusEnum::PENDING,
    ]);

    Payment::create([
        'payable_type' => Order::class,
        'payable_id' => $order->id,
        'order_id' => 'ORDER-'.uniqid(),
        'amount' => 11000,
        'expired_at' => now()->subHour(),
    ]);

    (new ExpirePayments)->handle();

    expect($order->fresh()->payment_status)->toBe(PaymentStatusEnum::EXPIRED);
});

test('non-expired payments are not affected', function () {
    $user = User::factory()->create();

    $category = PPOBCategory::create([
        'name' => 'Test Category',
        'slug' => 'test-category-'.uniqid(),
    ]);

    $brand = PPOBBrand::create([
        'p_p_o_b_category_id' => $category->id,
        'name' => 'Test Brand',
        'slug' => 'test-brand-'.uniqid(),
    ]);

    $order = Order::create([
        'user_id' => $user->id,
        'p_p_o_b_brand_id' => $brand->id,
        'reference' => 'TEST-'.uniqid(),
        'ref_number' => rand(1000000000, 9999999999),
        'name' => 'Test Order',
        'email' => 'test@example.com',
        'phone' => '08123456789',
        'amount' => 10000,
        'fee' => 1000,
        'total_amount' => 11000,
        'payment_status' => PaymentStatusEnum::PENDING,
    ]);

    Payment::create([
        'payable_type' => Order::class,
        'payable_id' => $order->id,
        'order_id' => 'ORDER-'.uniqid(),
        'amount' => 11000,
        'expired_at' => now()->addHour(),
    ]);

    (new ExpirePayments)->handle();

    expect($order->fresh()->payment_status)->toBe(PaymentStatusEnum::PENDING);
});

test('payments with non-pending status are not affected', function () {
    $user = User::factory()->create();

    $category = PPOBCategory::create([
        'name' => 'Test Category',
        'slug' => 'test-category-'.uniqid(),
    ]);

    $brand = PPOBBrand::create([
        'p_p_o_b_category_id' => $category->id,
        'name' => 'Test Brand',
        'slug' => 'test-brand-'.uniqid(),
    ]);

    $order = Order::create([
        'user_id' => $user->id,
        'p_p_o_b_brand_id' => $brand->id,
        'reference' => 'TEST-'.uniqid(),
        'ref_number' => rand(1000000000, 9999999999),
        'name' => 'Test Order',
        'email' => 'test@example.com',
        'phone' => '08123456789',
        'amount' => 10000,
        'fee' => 1000,
        'total_amount' => 11000,
        'payment_status' => PaymentStatusEnum::SETTLEMENT,
    ]);

    Payment::create([
        'payable_type' => Order::class,
        'payable_id' => $order->id,
        'order_id' => 'ORDER-'.uniqid(),
        'amount' => 11000,
        'expired_at' => now()->subHour(),
    ]);

    (new ExpirePayments)->handle();

    expect($order->fresh()->payment_status)->toBe(PaymentStatusEnum::SETTLEMENT);
});
