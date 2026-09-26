<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    config(['inertia.ssr.enabled' => false]);
});

it('rejects unsigned access to the transaction detail page', function () {
    $order = makeTestOrder();

    $this->get(route('transaction.show', ['order' => $order->reference]))
        ->assertForbidden();
});

it('allows access to the transaction detail page through a signed url', function () {
    $order = makeTestOrder();

    $url = URL::signedRoute('transaction.show', ['order' => $order->reference]);

    $this->get($url)->assertOk();
});

it('rejects an unsigned payment proof upload', function () {
    $order = makeTestOrder();

    $this->put(route('transaction.update', ['order' => $order->reference]), [
        'image' => UploadedFile::fake()->image('proof.jpg'),
    ])->assertForbidden();
});

it('rejects a payment proof upload for a non manual payment', function () {
    $order = makeTestOrder(['payment' => ['driver' => 'midtrans']]);

    $url = URL::signedRoute('transaction.update', ['order' => $order->reference]);

    $this->put($url, [
        'image' => UploadedFile::fake()->image('proof.jpg'),
    ])->assertForbidden();
});

it('accepts a payment proof upload for a pending manual payment', function () {
    $order = makeTestOrder();

    $url = URL::signedRoute('transaction.update', ['order' => $order->reference]);

    $this->put($url, [
        'image' => UploadedFile::fake()->image('proof.jpg'),
    ])->assertRedirect();

    expect($order->payment->fresh()->getMedia('image'))->toHaveCount(1);
});

it('rejects a payment proof upload once the payment is already paid', function () {
    $order = makeTestOrder(['payment' => ['paid_at' => now()]]);

    $url = URL::signedRoute('transaction.update', ['order' => $order->reference]);

    $this->put($url, [
        'image' => UploadedFile::fake()->image('proof.jpg'),
    ])->assertForbidden();
});
