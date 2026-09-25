<?php

namespace App\Actions\Api\V1\Callback;

use App\Enums\DigiflazzStatusEnum;
use App\Mail\TopupFailed;
use App\Mail\TopupSuccess;
use App\Models\Order\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Triyatna\Digiflazz\Helpers\Webhook;

class HandleDigiflazzCallbackAction
{
    public function handle(?string $signature, string $payload, array|string $data)
    {
        if (! is_array($data)) {
            $decoded = json_decode($data, true);
            $data = is_array($decoded) ? $decoded : [];
        }

        $this->assertData($data);

        /**
         * Signature validation
         */
        $secret = config('digiflazz.webhook_secret');

        if (! Webhook::validate($signature ?? '', $payload, $secret)) {
            throw new \Exception('Invalid signature', 403);
        }

        Log::info('Digiflazz Callback Received', [
            'ref_id' => $data['ref_id'],
            'status' => $data['status'],
            'rc' => $data['rc'] ?? null,
        ]);

        $result = DB::transaction(function () use ($data) {
            $order = Order::with('product')
                ->where('reference', $data['ref_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // Idempotent: ignore duplicate callbacks for an already-fulfilled order.
            if ($order->topup_status === DigiflazzStatusEnum::SUCCESS) {
                return null;
            }

            $isSuccess = $data['status'] === 'Sukses' && ($data['rc'] ?? '') === '00';

            $order->update(
                $isSuccess
                    ? [
                        'sn' => $data['sn'] ?? null,
                        'topup_status' => DigiflazzStatusEnum::SUCCESS,
                    ]
                    : [
                        'topup_status' => DigiflazzStatusEnum::FAILED,
                    ]
            );

            return [$order, $isSuccess];
        });

        if ($result === null) {
            return;
        }

        [$order, $isSuccess] = $result;

        $isSuccess
            ? $this->sendSuccessNotification($order)
            : $this->sendFailedNotification($order);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function assertData(array $data): void
    {
        foreach (['ref_id', 'status'] as $key) {
            if (! array_key_exists($key, $data)) {
                throw new \Exception('Invalid callback payload', 400);
            }
        }
    }

    protected function sendSuccessNotification(Order $order): void
    {
        $message = getSetting('template_order_completed') ?? '';
        $message = str_replace('{customer_name}', $order->name, $message);
        $message = str_replace('{order_id}', $order->reference, $message);
        $message = str_replace('{app_name}', config('app.name'), $message);
        $message = str_replace('{link}', transactionUrl($order), $message);
        $message = str_replace('{cs_link}', getSetting('cs'), $message);

        Mail::to($order->email)->send(new TopupSuccess($order));

        $order->notifications()->create([
            'provider' => 'email',
            'title' => 'Order Completed',
            'content' => $message,
            'error' => false,
        ]);
    }

    protected function sendFailedNotification(Order $order): void
    {
        $message = getSetting('template_order_failed') ?? '';
        $message = str_replace('{customer_name}', $order->name, $message);
        $message = str_replace('{order_id}', $order->reference, $message);
        $message = str_replace('{app_name}', config('app.name'), $message);
        $message = str_replace('{link}', transactionUrl($order), $message);
        $message = str_replace('{cs_link}', getSetting('cs'), $message);

        Mail::to($order->email)->send(new TopupFailed($order));

        $order->notifications()->create([
            'provider' => 'email',
            'title' => 'Order Failed',
            'content' => $message,
            'error' => false,
        ]);
    }
}
