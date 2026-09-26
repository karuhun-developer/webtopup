<?php

use App\Models\Order\Order;
use App\Models\PPOB\PPOBBrand;
use App\Models\PPOB\PPOBCategory;
use App\Models\PPOB\PPOBProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

/**
 * Build a persisted order (with its payment) for tests.
 *
 * @param  array<string, mixed>  $overrides  Order attributes; a `payment` key
 *                                          overrides the payment attributes.
 */
function makeTestOrder(array $overrides = []): Order
{
    $paymentOverrides = $overrides['payment'] ?? [];
    unset($overrides['payment']);

    $category = PPOBCategory::create([
        'name' => 'Games',
        'description' => 'Game vouchers',
        'status' => true,
    ]);

    $brand = PPOBBrand::create([
        'p_p_o_b_category_id' => $category->id,
        'name' => 'Mobile Legends',
        'provider' => 'digiflazz',
        'description' => 'MLBB diamonds',
        'featured' => true,
        'order' => 1,
        'status' => true,
    ]);

    $product = PPOBProduct::create([
        'p_p_o_b_brand_id' => $brand->id,
        'name' => '86 Diamonds',
        'sku' => 'ML86',
        'provider' => 'digiflazz',
        'buy_price' => 20000,
        'sell_price' => 22000,
        'status' => true,
    ]);

    $order = Order::create(array_merge([
        'p_p_o_b_brand_id' => $brand->id,
        'p_p_o_b_product_id' => $product->id,
        'reference' => 'TRX-TEST-'.Str::upper(Str::random(8)),
        'ref_number' => random_int(1, 1_000_000_000),
        'name' => 'Budi',
        'email' => 'budi@example.com',
        'phone' => '08123456789',
        'submited' => ['account_id' => '12345', 'server_id' => '1'],
        'amount' => 22000,
        'fee' => 0,
        'total_amount' => 22000,
        'payment_status' => 0,
        'topup_status' => 0,
    ], $overrides));

    $order->payment()->create(array_merge([
        'driver' => 'manual',
        'order_id' => uniqid().time(),
        'payment_type' => 'bank_transfer',
        'channel' => 'bca',
        'expired_at' => now()->addHours(24),
        'amount' => $order->total_amount,
    ], $paymentOverrides));

    return $order;
}
