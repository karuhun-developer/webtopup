<?php

use App\Actions\Cms\Setting\Setting\SaveSettingAction;
use App\Models\Setting\Setting;
use App\Models\User;
use App\Services\PaymentGatewayService;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    config(['inertia.ssr.enabled' => false]);
    Cache::flush();
});

it('defaults new stores to QRIS Karuhun and preserves a saved gateway when seeding again', function () {
    $this->seed(SettingSeeder::class);
    $gateway = app(PaymentGatewayService::class);

    expect($gateway->driver())->toBe('karuhun_qris')
        ->and(array_column($gateway->methods(), 'id'))->toBe(['qris'])
        ->and($gateway->fee('qris', 100000))->toBe(0);

    app(SaveSettingAction::class)->handle(['value' => ['payment_gateway' => 'midtrans']]);
    $this->seed(SettingSeeder::class);

    expect($gateway->driver())->toBe('midtrans')
        ->and(array_column($gateway->methods(), 'id'))->toContain('bca');
});

it('encrypts credentials and keeps blank values from overwriting existing secrets', function () {
    $action = app(SaveSettingAction::class);
    $action->handle(['value' => ['title' => 'Store'], 'credentials' => ['h2h_api_key' => 'secret-key']]);
    $action->handle(['value' => ['username_provider' => 'h2h'], 'credentials' => ['h2h_api_key' => '']]);

    expect(Setting::first()->credentials['h2h_api_key'])->toBe('secret-key')
        ->and(DB::table('settings')->value('credentials'))->not->toContain('secret-key')
        ->and(getSetting('title'))->toBe('Store');
});

it('does not expose credentials to public Inertia pages', function () {
    Setting::create(['value' => ['title' => 'Store'], 'credentials' => ['h2h_api_key' => 'never-public']]);
    $this->get(route('home'))->assertOk()->assertInertia(fn (Assert $page) => $page
        ->where('setting.title', 'Store')->missing('setting.credentials')->missing('setting.h2h_api_key'));
});

it('saves integration choices through the existing CMS settings page', function () {
    config(['app.url' => 'https://topup.example']);
    $this->seed(PermissionSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('superadmin');
    $this->actingAs($admin)->put(route('cms.setting.save'), [
        'value' => ['supplier_provider' => 'h2h', 'username_provider' => 'apigames', 'payment_gateway' => 'karuhun_qris'],
        'credentials' => ['h2h_api_key' => 'cms-key', 'qris_api_key' => 'qris-key'],
    ])->assertSessionHasNoErrors();
    expect(getSetting('username_provider'))->toBe('apigames');
    $this->get(route('cms.setting.index'))->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('cms/setting/setting/Index')->where('configuredCredentials.h2h_api_key', true)
        ->where('callbackUrls.h2h', 'https://topup.example'.route('api.v1.h2h.callback', [], false))
        ->where('callbackUrls.digiflazz', 'https://topup.example'.route('api.v1.digiflazz.callback', [], false))
        ->where('callbackUrls.midtrans', 'https://topup.example'.route('api.v1.midtrans.callback', [], false))
        ->where('callbackUrls.paywuz', 'https://topup.example'.route('api.v1.paywuz.callback', [], false))
        ->missing('callbackUrls.karuhun_qris')
        ->missing('setting.credentials'));
});

it('rejects unsupported integration providers', function () {
    $this->seed(PermissionSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('superadmin');
    $this->actingAs($admin)->put(route('cms.setting.save'), ['value' => ['payment_gateway' => 'unknown']])
        ->assertSessionHasErrors('value.payment_gateway');
});

it('opens the generic product import page with H2H as default and redirects the legacy URL', function () {
    $this->seed(PermissionSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('superadmin');
    Setting::create(['value' => [], 'credentials' => ['h2h_api_key' => 'cms-h2h']]);
    Http::preventStrayRequests();
    Http::fake(['*/api/v1/products*' => Http::response(['data' => []])]);

    $this->actingAs($admin)->get(route('cms.ppob.import-product.index'))->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('cms/ppob/import-product/Index')->where('provider', 'h2h'));
    $this->get('/cms/ppob/import-digiflazz')->assertRedirect('/cms/ppob/import-product');
    $this->post(route('cms.ppob.import-product.sync'), ['provider' => 'unknown'])->assertSessionHasErrors('provider');
});
