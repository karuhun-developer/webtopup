<?php

namespace App\Actions\Cms\Order\GiftOrder;

use App\Models\Order\Order;
use App\Services\VodaService;
use Illuminate\Support\Facades\Log;

class SendNotificationAction
{
    public function __construct(
        public readonly VodaService $vodaService,
    ) {}

    /**
     * Handle the action.
     */
    public function handle(Order $order, array $data): void
    {
        switch ($data['action']) {
            case 'admin_add_friend':
                // Message
                $message = getSetting('template_gift_order_admin_friend_request');
                $title = 'Permintaan Konfirmasi Teman';
                break;
            case 'user_confirm_friend':
                // Message
                $message = getSetting('template_gift_order_user_friend_confirmation');
                $title = 'Konfirmasi Teman Berhasil';
                break;
            case 'gift_send':
                $message = getSetting('template_gift_order_completion');
                $title = 'Pesanan Hadiah Selesai';
                break;
            default:
                throw new \InvalidArgumentException('Invalid notification action: '.$data['action']);
                break;
        }

        $message = str_replace('{app_name}', config('app.name'), $message);
        $message = str_replace('{customer_name}', $order->name, $message);
        $message = str_replace('{order_id}', $order->reference, $message);
        $message = str_replace('{cs_link}', getSetting('cs'), $message);

        // Send message via Voda
        $isNotificationError = false;
        try {
            $this->vodaService->sendMessage(
                phone: $order->phone,
                message: $message,
                linkPreview: true,
            );
        } catch (\Exception $e) {
            Log::error('Failed to send Voda message: '.$e->getMessage());
            $isNotificationError = true;
        }

        // Create notification record
        $order->notifications()->create([
            'provider' => 'voda',
            'title' => $title,
            'content' => $message,
            'error' => $isNotificationError,
        ]);
    }
}
