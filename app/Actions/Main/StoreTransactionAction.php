<?php

namespace App\Actions\Main;

use App\Models\Order\Order;
use App\Models\Payment\Payment;
use App\Models\PPOB\PPOBProduct;
use App\Services\MidtransService;
use App\Services\VodaService;
use App\Traits\WithGenerateReference;
use Illuminate\Support\Facades\Log;

class StoreTransactionAction
{
    use WithGenerateReference;

    public function __construct(
        public readonly MidtransService $midtransService,
        public readonly VodaService $vodaService,
    ) {}

    /**
     * Handle the action.
     */
    public function handle(array $data): Order
    {
        $reference = $this->generateReference(
            model: new Order,
            prefix: 'TRX-'.now()->format('Ymd').'-',
        );

        // Get price from product
        $product = PPOBProduct::find($data['product_id']);

        $data['reference'] = $reference['code'];
        $data['ref_number'] = $reference['number'];
        $data['amount'] = (int) $product->sell_price;
        $data['user_id'] = auth()->id() ?? null;

        // Get fee if payment type is automatic
        if ($data['payment_type'] === 'automatic') {
            $fee = $data['payment_method'] === 'qris'
                ? $product->price * 0.007 // 0.7% fee for qris
                : 4000; // Flat 4000 fee for bank transfer

            $data['fee'] = (int) round($fee);
            $data['total_amount'] = $data['amount'] + $data['fee'];
        } else {
            $data['fee'] = 0;
            $data['total_amount'] = $data['amount'];
        }

        // If brand provider is gift then add step to submited data
        if ($product->brand->provider === 'gift') {
            $data['submited'] = [
                ...$data['submited'],
                'admin_account_ign' => '',
                'admin_add_friend' => false,
                'admin_add_friend_timestamp' => '',
                'user_confirm_friend' => false,
                'user_confirm_friend_timestamp' => '',
                'gift_send' => false,
                'dispute' => false,
                'done' => false,
            ];
        }

        $order = Order::create($data);

        // Create payment record
        $orderId = uniqid().time();
        $payment = Payment::create([
            'driver' => $data['payment_type'] === 'automatic' ? 'midtrans' : 'manual',
            'payable_type' => Order::class,
            'payable_id' => $order->id,
            'order_id' => $orderId,
            'transaction_id' => null,
            'payment_type' => $data['payment_method'] === 'qris' ? 'qris' : 'bank_transfer',
            // AUTO_GENERATED
            'account_number' => 'AUTO_GENERATED',
            'channel' => $data['payment_method'],
            'expired_at' => now()->addHours(24),
            'amount' => $order->total_amount,
        ]);

        // Create Midtrans transaction if payment type is automatic
        if ($data['payment_type'] === 'automatic') {
            if ($data['payment_method'] === 'qris') {
                $midtrans = $this->midtransService->createQris(
                    orderId: $payment->order_id,
                    amount: $payment->amount,
                );
            } else {
                $midtrans = $this->midtransService->createBankTransfer(
                    orderId: $payment->order_id,
                    bank: $payment->channel,
                    amount: $payment->amount,
                );
            }

            // Throw an exception if Midtrans transaction creation failed
            if (! $midtrans['successful']) {
                throw new \Exception('Failed to create Midtrans transaction: '.$midtrans['message']);
            }

            // Update payment with Midtrans details
            $payment->transaction_id = $midtrans['transaction_id'];
            $payment->account_number = $midtrans['account'];
            $payment->account_code = $midtrans['code'] ?? null;
            $payment->save();
        } else {
            // For manual payment, we can set the account number to the payment method for easier reconciliation
            $payment->channel = getSetting('manual_transfer_bank');
            $payment->account_number = getSetting('manual_transfer_account_number');
            $payment->account_code = getSetting('manual_transfer_account_name');
            $payment->save();
        }

        // Send notification to user
        $message = getSetting('template_checkout');
        $message = str_replace('{app_name}', config('app.name'), $message);
        $message = str_replace('{order_id}', $order->reference, $message);
        $message = str_replace('{product}', $product->name, $message);
        $message = str_replace('{total}', numberToCurrency($order->total_amount), $message);
        $message = str_replace('{link}', route('transaction.show', [
            'order' => $order,
        ]), $message);
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
            'title' => 'Order Created',
            'content' => $message,
            'error' => $isNotificationError,
        ]);

        return $order;
    }
}
