<?php

namespace App\Actions\Main;

use App\Models\Order\Order;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Response;

class GetQrisImageAction
{
    public function __construct(public readonly PaymentGatewayService $gateways) {}

    public function handle(Order $order): Response
    {
        $payment = $order->payment;
        abort_unless($payment?->driver === 'karuhun_qris' && filled($payment->transaction_id), 404);
        $image = $this->gateways->client('karuhun_qris')->accept('image/*')
            ->get('/qr/'.rawurlencode($payment->transaction_id))->throw();
        $contentType = (string) $image->header('Content-Type');
        abort_unless(str_starts_with($contentType, 'image/'), 502);

        return response($image->body())->withHeaders([
            'Content-Type' => $contentType,
            'Cache-Control' => 'private, max-age=60',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
