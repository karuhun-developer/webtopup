<?php

use App\Actions\Cms\PPOB\ImportProduct\SyncPPOBProductAction;
use App\Actions\Cms\Setting\Setting\SaveSettingAction;
use App\Exceptions\ProductCatalogPendingException;
use App\Models\PPOB\PPOBProduct;
use App\Models\Setting\Setting;
use App\Models\User;
use App\Services\H2hService;
use App\Services\ProductProviderService;
use Database\Seeders\PermissionSeeder;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    config(['inertia.ssr.enabled' => false]);
    Cache::flush();
    Http::preventStrayRequests();
    Setting::create(['value' => [], 'credentials' => [
        'h2h_api_key' => 'catalog-test-key',
        'digiflazz_username' => 'test-buyer',
        'digiflazz_api_key' => 'test-digiflazz-key',
    ]]);
});

function h2hCatalogFixture(int $number): array
{
    return [
        'id' => 'product-'.$number, 'sku' => 'SKU-'.$number, 'name' => 'Product '.$number,
        'category' => 'Games', 'operator' => 'Mobile Legends', 'billingType' => 'prepaid',
        'apiPrice' => 20000, 'active' => true, 'published' => true, 'fields' => [],
    ];
}

it('excludes catalog read and import payloads from Debugbar before collectors are attached', function () {
    $debugbar = app('debugbar');
    expect($debugbar->requestIsExcluded(Request::create('/cms/ppob/import-product')))->toBeTrue()
        ->and($debugbar->requestIsExcluded(Request::create('/cms/ppob/import-product', 'POST')))->toBeTrue()
        ->and($debugbar->requestIsExcluded(Request::create('/cms/setting/settings')))->toBeFalse();
});

it('loads the catalog in batches and reuses the complete catalog for importing', function () {
    $products = array_map(h2hCatalogFixture(...), range(1, 501));
    Http::fake(fn ($request) => Http::response([
        'data' => array_slice($products, ((int) $request['page'] - 1) * 50, 50),
    ]));
    $service = app(H2hService::class);

    $first = $service->catalog();
    expect($first['complete'])->toBeFalse()->and($first['count'])->toBe(500);
    Http::assertSentCount(10);

    $complete = $service->catalog();
    expect($complete['complete'])->toBeTrue()
        ->and($complete['count'])->toBe(501)
        ->and($complete['products'][500]['sku'])->toBe('SKU-501');
    expect($service->products())->toHaveCount(501);
    Http::assertSentCount(11);
});

it('preserves progress on a provider rate limit and resumes the same page after cooldown', function () {
    Http::fake(['*/api/v1/products*' => Http::sequence()
        ->push(['data' => array_map(h2hCatalogFixture(...), range(1, 50))])
        ->push(['error' => ['code' => 'RATE_LIMITED']], 429, ['Retry-After' => '2'])
        ->push(['data' => [h2hCatalogFixture(51)]])]);
    $service = app(H2hService::class);

    $paused = $service->catalog();
    expect($paused['complete'])->toBeFalse()->and($paused['count'])->toBe(50)
        ->and($paused['retry_after'])->toBeGreaterThan(0);
    $service->catalog();
    Http::assertSentCount(2);

    $this->travel(3)->seconds();
    $complete = $service->catalog();
    expect($complete['complete'])->toBeTrue()->and($complete['count'])->toBe(51);
    Http::assertSentCount(3);
    Http::assertSent(fn ($request) => (int) $request['page'] === 2);
});

it('keeps the catalog within its local request budget and continues without restarting', function () {
    Http::fake(fn ($request) => Http::response([
        'data' => array_map(h2hCatalogFixture(...), range(((int) $request['page'] - 1) * 50 + 1, (int) $request['page'] * 50)),
    ]));
    $service = app(H2hService::class);
    for ($batch = 0; $batch < 9; $batch++) {
        $service->catalog();
    }
    $paused = $service->catalog();
    expect($paused['count'])->toBe(4500)->and($paused['retry_after'])->toBeGreaterThan(0);
    Http::assertSentCount(90);

    $this->travel(61)->seconds();
    $continued = $service->catalog();
    expect($continued['count'])->toBe(5000)->and($continued['complete'])->toBeFalse();
    Http::assertSentCount(100);
    Http::assertSent(fn ($request) => (int) $request['page'] === 91);
});

it('keeps successful pages when a later provider request fails', function () {
    Http::fake(['*/api/v1/products*' => Http::sequence()
        ->push(['data' => array_map(h2hCatalogFixture(...), range(1, 50))])
        ->push(['error' => ['code' => 'UNAVAILABLE']], 500)
        ->push(['data' => [h2hCatalogFixture(51)]])]);
    $service = app(H2hService::class);

    expect(fn () => $service->catalog())->toThrow(RequestException::class);
    $complete = $service->catalog();
    expect($complete['complete'])->toBeTrue()->and($complete['count'])->toBe(51);
    Http::assertSentCount(3);
});

it('does not import an incomplete catalog', function () {
    Http::fake(fn ($request) => Http::response([
        'data' => array_map(h2hCatalogFixture(...), range(((int) $request['page'] - 1) * 50 + 1, (int) $request['page'] * 50)),
    ]));

    expect(fn () => app(SyncPPOBProductAction::class)->handle('h2h'))
        ->toThrow(ProductCatalogPendingException::class);
    expect(PPOBProduct::count())->toBe(0);
    Http::assertSentCount(10);
});

it('does not mix catalogs from different H2H base URLs', function () {
    Http::fake([
        'h2h.karuhundeveloper.com/*' => Http::response(['data' => [h2hCatalogFixture(1)]]),
        'other-h2h.example/*' => Http::response(['data' => [h2hCatalogFixture(2)]]),
    ]);
    $service = app(H2hService::class);
    expect($service->catalog()['products'][0]['sku'])->toBe('SKU-1');
    app(SaveSettingAction::class)->handle(['value' => ['h2h_base_url' => 'https://other-h2h.example']]);
    expect($service->catalog()['products'][0]['sku'])->toBe('SKU-2');
    Http::assertSentCount(2);
});

it('returns partial catalog progress to the CMS without a credential error', function () {
    $this->seed(PermissionSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('superadmin');
    Http::fake(fn ($request) => Http::response([
        'data' => array_map(h2hCatalogFixture(...), range(((int) $request['page'] - 1) * 50 + 1, (int) $request['page'] * 50)),
    ]));

    $this->actingAs($admin)->get(route('cms.ppob.import-product.index'))->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('cms/ppob/import-product/Index')
            ->where('catalog.complete', false)->where('catalog.count', 500)->where('error', null)
            ->has('products.0.brands.0.products', 500));
    $this->post(route('cms.ppob.import-product.sync'), ['provider' => 'h2h'])
        ->assertSessionHasErrors(['provider' => 'Katalog H2H masih dimuat. Tunggu sampai daftar produk lengkap sebelum import.']);
    expect(PPOBProduct::count())->toBe(0);
});

it('explains provider authentication and availability errors accurately', function (int $status, string $message) {
    $this->seed(PermissionSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('superadmin');
    Http::fake(['*/api/v1/products*' => Http::response(['error' => ['code' => 'PROVIDER_ERROR']], $status)]);

    $this->actingAs($admin)->get(route('cms.ppob.import-product.index'))->assertOk()
        ->assertInertia(fn (Assert $page) => $page->where('error', $message));
})->with([
    [401, 'API key provider tidak valid. Periksa credential di Settings.'],
    [403, 'API key belum memiliki akses katalog. Periksa izin key di dashboard provider.'],
    [503, 'Provider belum dapat mengirim katalog. Coba kembali sebentar lagi.'],
]);

it('continues to load and import Digiflazz catalogs using CMS credentials', function () {
    Http::fake(['api.digiflazz.com/v1/price-list' => Http::response(['data' => [[
        'buyer_sku_code' => 'ML-86', 'product_name' => '86 Diamonds', 'category' => 'Games',
        'brand' => 'Mobile Legends', 'price' => 20000,
        'buyer_product_status' => true, 'seller_product_status' => true,
    ]]])]);

    $catalog = app(ProductProviderService::class)->catalog('digiflazz');
    expect($catalog['complete'])->toBeTrue()->and($catalog['count'])->toBe(1);
    app(SyncPPOBProductAction::class)->handle('digiflazz');
    expect(PPOBProduct::first()->provider)->toBe('digiflazz');
    Http::assertSent(fn ($request) => $request['username'] === 'test-buyer'
        && $request['sign'] === md5('test-buyertest-digiflazz-keypricelist'));
});
