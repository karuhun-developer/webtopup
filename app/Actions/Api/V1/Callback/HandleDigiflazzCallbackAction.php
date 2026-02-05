<?php

namespace App\Actions\Api\V1\Callback;

use App\Enums\DigiflazzStatusEnum;
use App\Models\Order\Order;
use App\Services\VodaService;
use Illuminate\Support\Facades\Log;
use Triyatna\Digiflazz\Helpers\Webhook;

class HandleDigiflazzCallbackAction
{
    public function __construct(
        public readonly VodaService $vodaService,
    ) {}

    public function handle(string $signature, string $payload, string $data)
    {
        Log::info('Digiflazz Callback Received', [
            'request' => $payload,
        ]);

        /**
         * Signature validation
         */
        $secret = config('digiflazz.webhook_secret');

        if (!Webhook::validate($signature, $payload, $secret)) throw new \Exception('Invalid signature');

        // Find order by reference ID
        $order = Order::where('reference', $data['ref_id'])->firstOrFail();

        // Update order status based on Digiflazz status
        if ($data['status'] === 'Sukses' && $data['rc'] === '00') {
            $order->update([
                'sn' => $data['sn'] ?? null,
                'topup_status' => DigiflazzStatusEnum::SUCCESS,
            ]);

            // Send notification to users
            $message = getSetting('template_order_completed');
            $message = str_replace('{customer_name}', $order->name, $message);
            $message = str_replace('{order_id}', $order->reference, $message);
            $message = str_replace('{app_name}', config('app.name'), $message);
            $message = str_replace('{link}', route('transaction.show', [
                'order' => $order,
            ]), $message);
            $message = str_replace('{cs_link}', getSetting('cs'), $message);

            // Send message via Voda
            $this->vodaService->sendMessage(
                phone: $order->phone,
                message: $message,
                linkPreview: true,
            );
        }
    }
}
