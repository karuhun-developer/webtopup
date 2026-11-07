<?php

use App\Actions\Api\V1\Callback\HandleDigiflazzCallbackAction;
use App\Actions\Cms\PPOB\ImportProduct\SyncPPOBProductAction;
use App\Actions\Cms\PPOB\PPOBBrand\UpdatePPOBBrandAction;
use App\Actions\Cms\Setting\Setting\SaveSettingAction;
use App\Enums\DigiflazzStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Jobs\CheckGatewayPayments;
use App\Jobs\CreateGatewayPayment;
use App\Jobs\SubmitOrderToProvider;
use App\Mail\TopupSuccess;
use App\Models\Order\Order;
use App\Models\PPOB\PPOBProduct;
use App\Models\Setting\Setting;
use App\Services\PaymentGatewayService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    config(['inertia.ssr.enabled' => false]);
    Cache::flush();
    Mail::fake();
    Queue::fake();
    Http::preventStrayRequests();
    Setting::create(['value' => [], 'credentials' => [
        'h2h_api_key' => 'h2h-key', 'h2h_webhook_secret' => 'h2h-webhook',
        'qris_api_key' => 'qris-key', 'paywuz_api_key' => 'paywuz-key',
        'digiflazz_username' => 'buyer', 'digiflazz_api_key' => 'digiflazz-key',
    ]]);
});

it('imports H2H catalog without replacing products or edited selling prices', function () {
    $order = makeTestOrder();
    Http::fake(['*/api/v1/products*' => Http::response(['data' => [[
        'id' => 'h2h-product', 'sku' => $order->product->sku, 'name' => '86 Diamonds', 'category' => 'Games',
        'operator' => 'Mobile Legends', 'apiPrice' => 20000, 'billingType' => 'prepaid', 'active' => true,
        'published' => true, 'fields' => [], 'usernameService' => 'CEKML',
    ]]])]);
    $sync = app(SyncPPOBProductAction::class);
    $sync->handle('h2h');
    $product = PPOBProduct::where('provider', 'h2h')->firstOrFail();
    $product->update(['sell_price' => 30000]);
    $sync->handle('h2h');

    expect(PPOBProduct::count())->toBe(2)
        ->and($product->fresh()->sell_price)->toBe(30000)
        ->and($product->provider_product_id)->toBe('h2h-product')
        ->and($order->product->fresh()->provider)->toBe('digiflazz');
});

it('submits H2H orders with the saved payload and checks pending status without another purchase', function () {
    $order = makeTestOrder(['provider' => 'h2h', 'payment_status' => PaymentStatusEnum::SETTLEMENT]);
    $order->product->update(['provider' => 'h2h', 'provider_product_id' => 'h2h-product']);
    Http::fake([
        '*/api/v1/orders' => Http::response(['data' => ['order' => ['id' => 'h2h-order', 'status' => 'queued']]], 201),
        '*/api/v1/orders/h2h-order' => Http::response(['data' => ['id' => 'h2h-order', 'status' => 'success', 'serial' => 'SN-1']]),
    ]);
    $job = new SubmitOrderToProvider($order->id);
    app()->call([$job, 'handle']);
    app()->call([$job, 'handle']);
    app()->call([$job, 'handle']);

    expect($order->fresh()->topup_status)->toBe(DigiflazzStatusEnum::SUCCESS)
        ->and($order->fresh()->sn)->toBe('SN-1');
    Http::assertSentCount(2);
    Http::assertSent(fn ($request) => $request->method() === 'POST' && $request['productId'] === 'h2h-product'
        && $request['expectedPrice'] === 20000 && $request['serverId'] === '1'
        && $request->hasHeader('Idempotency-Key', $order->reference));
});

it('does not send unpaid orders to a supplier', function () {
    $order = makeTestOrder(['provider' => 'h2h']);
    app()->call([new SubmitOrderToProvider($order->id), 'handle']);
    Http::assertNothingSent();
});

it('reconciles ambiguous H2H failures without marking a potentially paid topup failed', function () {
    $order = makeTestOrder(['provider' => 'h2h', 'payment_status' => PaymentStatusEnum::SETTLEMENT]);
    (new SubmitOrderToProvider($order->id))->failed(new RuntimeException('timeout'));
    expect($order->fresh()->topup_status)->toBe(DigiflazzStatusEnum::ON_PROGRESS);
});

it('creates QRIS Karuhun with the exact unique amount and gateway expiry', function () {
    $order = makeTestOrder(['payment' => ['driver' => 'karuhun_qris', 'payment_type' => 'qris']]);
    $expires = now()->addMinutes(15)->toISOString();
    Http::fake(['*/v1/qris/create' => Http::response(['success' => true, 'data' => [
        'id' => 'qr-id', 'token' => 'secret-token', 'amount' => 22007,
        'qris_url' => 'https://qris.karuhundeveloper.com/qr/qr-id', 'expires_at' => $expires,
    ]], 201)]);
    app(PaymentGatewayService::class)->create($order->payment);
    app(PaymentGatewayService::class)->create($order->payment);

    expect($order->fresh()->total_amount)->toBe(22007)
        ->and($order->payment->fresh()->unique_amount)->toBe(7)
        ->and($order->payment->fresh()->gateway_token)->toBe('secret-token')
        ->and($order->payment->fresh()->toArray())->not->toHaveKey('gateway_token');
    Http::assertSent(fn ($request) => $request['amount'] === 22000 && $request->hasHeader('Idempotency-Key', $order->payment->order_id));
    Http::assertSentCount(1);
});

it('settles QRIS through token polling idempotently after a gateway switch', function () {
    $order = makeTestOrder(['payment' => ['driver' => 'karuhun_qris', 'gateway_token' => 'qr-token']]);
    Setting::first()->update(['value' => ['payment_gateway' => 'midtrans']]);
    Http::fake(['*/v1/payment/check' => Http::response(['success' => true, 'paid' => true, 'data' => ['token' => 'qr-token', 'amount' => 22000]])]);
    app()->call([new CheckGatewayPayments, 'handle']);
    app()->call([new CheckGatewayPayments, 'handle']);

    expect($order->fresh()->payment_status)->toBe(PaymentStatusEnum::SETTLEMENT);
    Queue::assertPushed(SubmitOrderToProvider::class, 1);
    Http::assertSentCount(1);
});

it('does not settle a QRIS response for the wrong token or amount', function (string $token, int $amount) {
    $order = makeTestOrder(['payment' => ['driver' => 'karuhun_qris', 'gateway_token' => 'qr-token']]);
    Http::fake(['*/v1/payment/check' => Http::response(['success' => true, 'paid' => true, 'data' => ['token' => $token, 'amount' => $amount]])]);
    app()->call([new CheckGatewayPayments, 'handle']);
    expect($order->fresh()->payment_status)->toBe(PaymentStatusEnum::PENDING);
    Queue::assertNothingPushed();
})->with([['wrong', 22000], ['qr-token', 21999]]);

it('uses Paywuz payment method fees and the returned checkout amount', function () {
    Setting::first()->update(['value' => ['payment_gateway' => 'paywuz']]);
    Http::fake([
        '*/v1/payment-methods' => Http::response(['data' => [[
            'code' => 'QRIS', 'name' => 'QRIS', 'type' => 'qris', 'fee' => ['flatIdr' => 290, 'percentBps' => 70],
            'limits' => ['minIdr' => 10000, 'maxIdr' => 50000000],
        ]]]),
        '*/v1/transactions' => Http::response(['data' => [
            'id' => 'paywuz-id', 'paymentUrl' => 'https://paywuz.id/pay/paywuz-id', 'paymentNumber' => 'qr-string',
            'totalPayment' => 22444, 'expiresAt' => now()->addHour()->toISOString(),
        ]], 201),
    ]);
    $gateway = app(PaymentGatewayService::class);
    expect($gateway->fee('QRIS', 22000))->toBe(444);
    $order = makeTestOrder(['fee' => 444, 'total_amount' => 22444, 'payment' => ['driver' => 'paywuz', 'channel' => 'QRIS', 'amount' => 22444]]);
    $gateway->create($order->payment);
    expect($order->payment->fresh()->payment_url)->toBe('https://paywuz.id/pay/paywuz-id');
    Http::assertSent(fn ($request) => $request->method() === 'POST' && $request['amount'] === 22000 && $request['feeByMerchant'] === false);
});

it('verifies Paywuz raw-body signatures and processes paid callbacks only once', function () {
    $order = makeTestOrder(['payment' => ['driver' => 'paywuz', 'transaction_id' => 'paywuz-id']]);
    $payload = ['event' => 'transaction.paid', 'data' => [
        'id' => 'paywuz-id', 'orderId' => $order->payment->order_id, 'totalPayment' => 22000,
    ]];
    $body = json_encode($payload);
    $headers = ['CONTENT_TYPE' => 'application/json', 'HTTP_X_PAYWUZ_SIGNATURE' => 'sha256='.hash_hmac('sha256', $body, 'paywuz-key')];
    $this->call('POST', route('api.v1.paywuz.callback'), [], [], [], $headers, $body)->assertOk();
    $this->call('POST', route('api.v1.paywuz.callback'), [], [], [], $headers, $body)->assertOk();
    Queue::assertPushed(SubmitOrderToProvider::class, 1);
    expect($order->fresh()->payment_status)->toBe(PaymentStatusEnum::SETTLEMENT);
});

it('rejects unsigned Paywuz callbacks', function () {
    $this->postJson(route('api.v1.paywuz.callback'), ['event' => 'transaction.paid'])->assertForbidden();
    Queue::assertNothingPushed();
});

it('verifies H2H signatures and handles repeated order events once', function () {
    $order = makeTestOrder(['provider' => 'h2h', 'provider_order_id' => 'h2h-order', 'payment_status' => PaymentStatusEnum::SETTLEMENT]);
    $timestamp = (string) now()->timestamp;
    $body = json_encode(['eventId' => 'event-1', 'type' => 'order.success', 'order' => [
        'id' => 'h2h-order', 'reference' => $order->reference, 'serial' => 'SN-1',
    ]]);
    $headers = ['CONTENT_TYPE' => 'application/json', 'HTTP_X_H2H_EVENT' => 'event-1', 'HTTP_X_H2H_TIMESTAMP' => $timestamp,
        'HTTP_X_H2H_SIGNATURE' => hash_hmac('sha256', $timestamp.'.'.$body, 'h2h-webhook')];
    $this->call('POST', route('api.v1.h2h.callback'), [], [], [], $headers, $body)->assertOk();
    $this->call('POST', route('api.v1.h2h.callback'), [], [], [], $headers, $body)->assertOk();
    expect($order->fresh()->topup_status)->toBe(DigiflazzStatusEnum::SUCCESS);
    Mail::assertQueued(TopupSuccess::class, 1);
});

it('rejects stale or unsigned H2H callbacks', function () {
    $this->postJson(route('api.v1.h2h.callback'), [])->assertForbidden();
    $timestamp = (string) now()->subMinutes(6)->timestamp;
    $body = '{}';
    $this->call('POST', route('api.v1.h2h.callback'), [], [], [], [
        'CONTENT_TYPE' => 'application/json', 'HTTP_X_H2H_TIMESTAMP' => $timestamp,
        'HTTP_X_H2H_SIGNATURE' => hash_hmac('sha256', $timestamp.'.'.$body, 'h2h-webhook'),
    ], $body)->assertForbidden();
});

it('retains checkout after a QRIS timeout and retries the original payment and provider selector', function () {
    app(SaveSettingAction::class)->handle([
        'value' => ['payment_gateway' => 'karuhun_qris', 'qris_provider' => 'gopay:default'],
    ]);
    $fixture = makeTestOrder();
    $fixture->product->update(['provider' => 'h2h', 'provider_product_id' => 'h2h-product']);
    $requests = [];
    Http::fake([
        '*/api/v1/username-services' => Http::response(['data' => [['service' => 'CEKML', 'game' => 'Mobile Legends', 'active' => true]]]),
        '*/api/v1/username-checks' => Http::response(['data' => ['id' => 'check-id', 'status' => 'success', 'username' => 'Player']]),
        '*/v1/qris/create' => function ($request) use (&$requests) {
            $requests[] = ['body' => $request->data(), 'key' => $request->header('Idempotency-Key')];
            if (count($requests) === 1) {
                return Http::failedConnection();
            }

            return Http::response(['success' => true, 'data' => [
                'id' => 'qris-id', 'token' => 'qris-token', 'amount' => 22007,
                'qris_url' => 'https://qris.karuhundeveloper.com/qr/id', 'expires_at' => now()->addMinutes(15)->toISOString(),
            ]], 201);
        },
    ]);
    $this->post(route('checkout.store'), [
        'type' => 'id+server', 'account_id' => '12345', 'server_id' => '1', 'product_id' => $fixture->product->id,
        'name' => 'Budi', 'email' => 'budi@example.com', 'phone' => '08123456789',
        'payment_type' => 'automatic', 'payment_method' => 'qris',
    ])->assertSessionHasNoErrors()->assertRedirect();

    $order = Order::latest('id')->firstOrFail();
    expect(Order::count())->toBe(2)->and($order->provider)->toBe('h2h')
        ->and((int) $order->fee)->toBe(0)
        ->and($order->provider_payload['productId'])->toBe('h2h-product')
        ->and($order->payment->transaction_id)->toBeNull();
    Queue::assertPushed(CreateGatewayPayment::class, 1);
    app(SaveSettingAction::class)->handle(['value' => ['qris_provider' => 'shopee:other']]);
    app()->call([new CreateGatewayPayment($order->payment->id), 'handle']);

    expect($requests)->toHaveCount(2)->and($requests[1])->toBe($requests[0])
        ->and($requests[1]['body']['provider'])->toBe('gopay:default')
        ->and($order->fresh()->total_amount)->toBe(22007)
        ->and($order->payment->fresh()->transaction_id)->toBe('qris-id');
});

it('recovers a Midtrans payment created before a response was lost', function () {
    config(['midtrans.server_key' => 'midtrans-key']);
    $order = makeTestOrder(['payment' => ['driver' => 'midtrans', 'channel' => 'bca']]);
    Http::fake([
        '*/v2/charge' => Http::response(['status_code' => '406', 'status_message' => 'Duplicate order ID'], 406),
        '*/v2/*/status' => Http::response([
            'status_code' => '200', 'transaction_id' => 'midtrans-id', 'order_id' => $order->payment->order_id,
            'gross_amount' => '22000.00', 'va_numbers' => [['bank' => 'bca', 'va_number' => '123456789']],
        ]),
    ]);
    app(PaymentGatewayService::class)->create($order->payment);
    app(PaymentGatewayService::class)->create($order->payment);
    expect($order->payment->fresh()->transaction_id)->toBe('midtrans-id')
        ->and($order->payment->fresh()->account_number)->toBe('123456789');
    Http::assertSentCount(2);
    Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Basic '.base64_encode('midtrans-key:')));
});

it('keeps Digiflazz pending transactions in progress and uses the original supplier snapshot', function () {
    $order = makeTestOrder(['provider' => 'digiflazz', 'provider_payload' => ['sku' => 'ML86'], 'payment_status' => PaymentStatusEnum::SETTLEMENT]);
    $order->product->update(['provider' => 'h2h', 'sku' => 'NEW-SKU']);
    Http::fake(['*/v1/transaction' => Http::response(['data' => ['rc' => '03', 'status' => 'Pending', 'sn' => '']])]);
    app()->call([new SubmitOrderToProvider($order->id), 'handle']);

    expect($order->fresh()->topup_status)->toBe(DigiflazzStatusEnum::ON_PROGRESS)
        ->and(Order::fromProviders(['digiflazz'])->whereKey($order->id)->exists())->toBeTrue();
    Http::assertSent(fn ($request) => $request['buyer_sku_code'] === 'ML86' && $request['ref_id'] === $order->reference);
    Mail::assertNothingQueued();
});

it('keeps pending Digiflazz callbacks pending after the product provider changes', function () {
    app(SaveSettingAction::class)->handle(['credentials' => ['digiflazz_webhook_secret' => 'webhook-key']]);
    $order = makeTestOrder(['provider' => 'digiflazz', 'payment_status' => PaymentStatusEnum::SETTLEMENT]);
    $order->product->update(['provider' => 'h2h']);
    $data = ['ref_id' => $order->reference, 'status' => 'Pending', 'rc' => '03'];
    $body = json_encode(['data' => $data]);
    app(HandleDigiflazzCallbackAction::class)->handle('sha1='.hash_hmac('sha1', $body, 'webhook-key'), $body, $data);

    expect($order->fresh()->topup_status)->toBe(DigiflazzStatusEnum::ON_PROGRESS);
    Mail::assertNothingQueued();
});

it('keeps mixed product providers when the brand provider is edited', function () {
    $order = makeTestOrder();
    $other = PPOBProduct::create([
        'p_p_o_b_brand_id' => $order->brand->id, 'name' => 'H2H Diamonds', 'sku' => 'ML86',
        'provider' => 'h2h', 'provider_product_id' => 'h2h-product', 'buy_price' => 20000, 'sell_price' => 22000, 'status' => true,
    ]);
    app(UpdatePPOBBrandAction::class)->handle($order->brand, ['provider' => 'h2h']);
    expect($order->product->fresh()->provider)->toBe('digiflazz')->and($other->fresh()->provider)->toBe('h2h');
});

it('checks QRIS from the signed detail page and shares the five second check limit with reconciliation', function () {
    $order = makeTestOrder(['payment' => [
        'driver' => 'karuhun_qris', 'payment_type' => 'qris', 'transaction_id' => 'qris-id', 'gateway_token' => 'qr-token',
    ]]);
    Http::fake(['*/v1/payment/check' => Http::sequence()
        ->push(['success' => true, 'paid' => false])
        ->push(['success' => true, 'paid' => true, 'data' => ['token' => 'qr-token', 'amount' => 22000]])]);

    $this->get(transactionUrl($order))->assertOk()->assertInertia(fn (AssertableInertia $page) => $page
        ->where('order.payment_status', 0)->missing('order.payment.gateway_token')
        ->where('order.payment.account_number', fn ($url) => str_contains($url, '/qris-image?signature=')));
    app()->call([new CheckGatewayPayments, 'handle']);
    Http::assertSentCount(1);

    $this->travel(5)->seconds();
    $this->get(transactionUrl($order))->assertOk()->assertInertia(fn (AssertableInertia $page) => $page->where('order.payment_status', 2));
    Http::assertSentCount(2);
    Http::assertSent(fn ($request) => $request['token'] === 'qr-token' && $request['wait'] === false);
    Queue::assertPushed(SubmitOrderToProvider::class, 1);
});

it('only reconciles orders whose payment is still pending', function () {
    makeTestOrder(['payment_status' => PaymentStatusEnum::DENY, 'payment' => [
        'driver' => 'karuhun_qris', 'gateway_token' => 'qr-token', 'transaction_id' => 'qris-id',
    ]]);
    app()->call([new CheckGatewayPayments, 'handle']);
    Http::assertNothingSent();
    Queue::assertNothingPushed();
});

it('reconciles a paid QRIS even when its invoice deadline has passed', function () {
    $order = makeTestOrder(['payment' => [
        'driver' => 'karuhun_qris', 'transaction_id' => 'qris-id', 'gateway_token' => 'qr-token', 'expired_at' => now()->subMinute(),
    ]]);
    Http::fake(['*/v1/payment/check' => Http::response(['success' => true, 'paid' => true, 'data' => ['token' => 'qr-token', 'amount' => 22000]])]);
    app()->call([new CheckGatewayPayments, 'handle']);
    expect($order->fresh()->payment_status)->toBe(PaymentStatusEnum::SETTLEMENT);
    Queue::assertPushed(SubmitOrderToProvider::class, 1);
});

it('expires an unpaid QRIS only after checking its final payment status', function () {
    $order = makeTestOrder(['payment' => [
        'driver' => 'karuhun_qris', 'transaction_id' => 'qris-id', 'gateway_token' => 'qr-token', 'expired_at' => now()->subMinute(),
    ]]);
    Http::fake(['*/v1/payment/check' => Http::response(['success' => true, 'paid' => false])]);
    app()->call([new CheckGatewayPayments, 'handle']);
    app()->call([new CheckGatewayPayments, 'handle']);
    expect($order->fresh()->payment_status)->toBe(PaymentStatusEnum::EXPIRED);
    Http::assertSentCount(1);
    Queue::assertNothingPushed();
});

it('keeps an overdue pending QRIS available for reconciliation when its status response is invalid', function (array $response) {
    $order = makeTestOrder(['payment' => [
        'driver' => 'karuhun_qris', 'transaction_id' => 'qris-id', 'gateway_token' => 'qr-token', 'expired_at' => now()->subMinute(),
    ]]);
    Http::fake(['*/v1/payment/check' => Http::response($response)]);
    app()->call([new CheckGatewayPayments, 'handle']);
    expect($order->fresh()->payment_status)->toBe(PaymentStatusEnum::PENDING);
    Queue::assertNothingPushed();
})->with([
    [['success' => false, 'paid' => false]],
    [['success' => true, 'paid' => true, 'data' => ['token' => 'wrong-token', 'amount' => 22000]]],
]);

it('serves the QRIS image through a signed server request without exposing its API key', function () {
    $order = makeTestOrder(['payment_status' => PaymentStatusEnum::SETTLEMENT, 'payment' => [
        'driver' => 'karuhun_qris', 'transaction_id' => 'qris-image-id',
    ]]);
    Http::fake(['*/qr/qris-image-id' => Http::response('<svg xmlns="http://www.w3.org/2000/svg"></svg>', 200, ['Content-Type' => 'image/svg+xml'])]);

    $this->get(route('transaction.qris-image', ['order' => $order->reference]))->assertForbidden();
    Http::assertNothingSent();
    $this->get(URL::signedRoute('transaction.qris-image', ['order' => $order->reference]))
        ->assertOk()->assertHeader('Content-Type', 'image/svg+xml')
        ->assertHeader('X-Content-Type-Options', 'nosniff');
    Http::assertSent(fn ($request) => $request->url() === 'https://qris.karuhundeveloper.com/qr/qris-image-id'
        && $request->hasHeader('x-api-key', 'qris-key'));
});

it('rejects non image responses from the QRIS image endpoint', function () {
    $order = makeTestOrder(['payment' => ['driver' => 'karuhun_qris', 'transaction_id' => 'qris-image-id']]);
    Http::fake(['*/qr/qris-image-id' => Http::response('invalid image', 200, ['Content-Type' => 'text/plain'])]);
    $this->get(URL::signedRoute('transaction.qris-image', ['order' => $order->reference]))->assertStatus(502);
});

it('keeps the QRIS Karuhun application fee at zero for every amount', function (int $amount) {
    Setting::first()->update(['value' => ['payment_gateway' => 'karuhun_qris']]);
    expect(app(PaymentGatewayService::class)->fee('qris', $amount))->toBe(0);
})->with([1000, 22000, 1000000]);
