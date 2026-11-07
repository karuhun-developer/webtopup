<?php

use App\Models\Account\Account;
use App\Models\Setting\Setting;
use App\Services\UsernameService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    config(['inertia.ssr.enabled' => false]);
    Cache::flush();
    Http::preventStrayRequests();
    Setting::create(['value' => [], 'credentials' => [
        'h2h_api_key' => 'h2h-test', 'apigames_merchant_id' => 'merchant', 'apigames_secret_key' => 'secret',
    ]]);
    Http::fake([
        '*/api/v1/username-services' => Http::response(['data' => [
            ['service' => 'CEKML', 'game' => 'Mobile Legends', 'active' => true],
            ['service' => 'CEKFF', 'game' => 'Free Fire', 'active' => true],
        ]]),
        '*/api/v1/username-checks' => Http::response(['data' => ['id' => 'check-1', 'status' => 'success', 'username' => 'KaruhunPlayer']], 201),
        '*/merchant/merchant/cek-username/*' => Http::response(['status' => 1, 'data' => ['username' => 'ApiGamesPlayer']]),
    ]);
});

it('checks through H2H by default and stores repeated requests only once', function () {
    $order = makeTestOrder();
    $payload = ['slug' => $order->brand->slug, 'account_id' => '0012345', 'server_id' => '2001'];

    $this->postJson(route('check-game-account'), $payload)->assertOk()->assertJsonPath('data.username', 'KaruhunPlayer');
    $this->postJson(route('check-game-account'), $payload)->assertOk();

    expect(Account::count())->toBe(1);
    Http::assertSentCount(2);
    Http::assertSent(fn ($request) => $request->url() === 'https://h2h.karuhundeveloper.com/api/v1/username-checks'
        && $request['destination'] === '0012345' && $request['serverId'] === '2001'
        && $request->hasHeader('X-API-Key', 'h2h-test') && $request->hasHeader('Idempotency-Key'));
});

it('does not reuse an account from another server or provider', function () {
    $username = app(UsernameService::class);
    $username->resolveAccount('mobilelegend', '12345', '2001');
    $username->resolveAccount('mobilelegend', '12345', '2002');
    $result = $username->resolveAccount('mobilelegend', '12345', '2001', 'apigames');

    expect(Account::count())->toBe(3)->and($result['data']['username'])->toBe('ApiGamesPlayer');
});

it('treats absent and empty server as the same identity', function () {
    $username = app(UsernameService::class);
    $username->resolveAccount('freefire', '12345');
    $username->resolveAccount('freefire', '12345', '');

    expect(Account::count())->toBe(1);
    Http::assertSentCount(2);
});

it('enforces account identity uniqueness in the database', function () {
    $attributes = ['game' => 'mobilelegend', 'uid' => '12345', 'server' => '2001', 'username' => 'Player'];
    Account::create($attributes);

    expect(fn () => Account::create($attributes))->toThrow(QueryException::class);
});

it('polls a pending username check without charging another request', function () {
    Http::swap(new Factory);
    Http::preventStrayRequests();
    Http::fake([
        '*/api/v1/username-services' => Http::response(['data' => [['service' => 'CEKML', 'game' => 'Mobile Legends', 'active' => true]]]),
        '*/api/v1/username-checks' => Http::response(['data' => ['id' => 'pending-check', 'status' => 'pending', 'username' => null]], 201),
        '*/api/v1/username-checks/pending-check' => Http::response(['data' => ['id' => 'pending-check', 'status' => 'success', 'username' => 'Player']]),
    ]);
    $username = app(UsernameService::class);
    expect($username->resolveAccount('mobilelegend', '12345', '2001')['pending'])->toBeTrue();
    expect(Account::count())->toBe(0);
    expect($username->resolveAccount('mobilelegend', '12345', '2001')['status'])->toBeTrue();

    Http::assertSentCount(3);
    expect(Account::count())->toBe(1);
});

it('uses the CMS username provider during checkout as well as the check endpoint', function () {
    Setting::first()->update(['value' => ['username_provider' => 'apigames']]);
    Cache::flush();
    $order = makeTestOrder();
    $this->postJson(route('check-game-account'), ['slug' => $order->brand->slug, 'account_id' => '12345', 'server_id' => '2001'])
        ->assertOk()->assertJsonPath('data.username', 'ApiGamesPlayer');
    Mail::fake();
    $this->post(route('checkout.store'), [
        'type' => 'id+server', 'account_id' => '12345', 'server_id' => '2001', 'product_id' => $order->product->id,
        'name' => 'Budi', 'email' => 'budi@example.com', 'phone' => '08123456789', 'payment_type' => 'manual', 'payment_method' => 'manual',
    ])->assertSessionHasNoErrors()->assertRedirect();
    expect(Account::count())->toBe(1);
    Http::assertSentCount(1);
});

it('deduplicates legacy accounts and preserves the latest username during migration', function () {
    $migration = require database_path('migrations/2026_11_07_140425_add_provider_identity_to_accounts_table.php');
    $migration->down();
    foreach ([[null, 'Old'], ['', 'Latest'], ['2001', 'Other server']] as [$server, $username]) {
        DB::table('accounts')->insert([
            'game' => 'freefire', 'uid' => '0012345', 'server' => $server, 'username' => $username,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }
    $migration->up();

    expect(Account::count())->toBe(2)
        ->and(Account::where('server', '')->firstOrFail()->username)->toBe('Latest')
        ->and(Account::where('server', '')->firstOrFail()->provider)->toBe('apigames')
        ->and(Account::where('server', '')->firstOrFail()->identity_key)->toBe(Account::identity('apigames', 'freefire', '0012345', ''));
});
