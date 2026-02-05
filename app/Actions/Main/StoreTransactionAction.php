<?php

namespace App\Actions\Main;

use App\Models\Order\Order;
use App\Models\Payment\Payment;
use App\Models\PPOB\PPOBProduct;
use App\Services\MidtransService;
use App\Traits\WithGenerateReference;

class StoreTransactionAction
{
    use WithGenerateReference;

    public function __construct(
        public readonly MidtransService $midtransService,
    ) {}

    /**
     * Handle the action.
     */
    public function handle(array $data): Order
    {
        $reference = $this->generateReference(
            model: new Order,
            prefix: 'TRX-' . now()->format('Ymd') . '-',
        );

        // Get price from product
        $product = PPOBProduct::find($data['product_id']);

        $data['reference'] = $reference['code'];
        $data['ref_number'] = $reference['number'];
        $data['amount'] = (int) $product->sell_price;

        // Get fee if payment type is automatic
        if ($data['payment_type'] === 'automatic') {
            $fee = $data['payment_method'] === 'qris'
                ? $product->price * 0.007 // 0.7% fee for qris
                : 4000; // Flat 4000 fee for bank transfer

            $data['fee'] = (int) round($fee);
            $data['total_amount'] = $data['amount'] + $data['fee'];
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
        }

        return $order;
    }
}
