<?php

namespace App\Actions\Main;

use App\Jobs\CreateGatewayPayment;
use App\Mail\OrderCreated;
use App\Models\Order\Order;
use App\Models\Payment\Payment;
use App\Models\PPOB\PPOBProduct;
use App\Models\Voucher\Voucher;
use App\Models\Voucher\VoucherUse;
use App\Services\PaymentGatewayService;
use App\Traits\WithGenerateReference;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class StoreTransactionAction
{
    use WithGenerateReference;

    public function __construct(
        public readonly PaymentGatewayService $paymentGateway,
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

        // Voucher Logic
        $voucher = null;
        $discountAmount = 0;
        if (isset($data['voucher_code']) && $data['voucher_code']) {
            $voucher = Voucher::where('code', $data['voucher_code'])->first();

            if ($voucher) {
                // Basic validation (should match checkVoucher logic)
                $now = now();
                $isValid = $voucher->status
                    && (! $voucher->start_date || $now->gte($voucher->start_date))
                    && (! $voucher->end_date || $now->lte($voucher->end_date))
                    && ($voucher->usage_limit === 0 || $voucher->used_count < $voucher->usage_limit)
                    && ($voucher->min_purchase_amount === 0 || $data['amount'] >= $voucher->min_purchase_amount);

                if ($isValid) {
                    if ($voucher->type === 'FIXED_AMOUNT') {
                        $discountAmount = $voucher->fixed_amount;
                    } elseif ($voucher->type === 'PERCENTAGE') {
                        $discountAmount = ($data['amount'] * $voucher->percentage) / 100;
                    }
                    $discountAmount = min($discountAmount, $data['amount']);
                }
            }
        }

        // Apply discount
        $data['discount_amount'] = $discountAmount;
        $amountAfterDiscount = $data['amount'] - $discountAmount;

        // Get fee if payment type is automatic
        if ($data['payment_type'] === 'automatic') {
            $data['fee'] = $this->paymentGateway->fee($data['payment_method'], (int) $amountAfterDiscount);
            $data['total_amount'] = $amountAfterDiscount + $data['fee'];
        } else {
            $data['fee'] = 0;
            $data['total_amount'] = $amountAfterDiscount;
        }

        // If product provider is gift then add step to submited data
        if ($product->provider === 'gift') {
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

        // If product provider is manual_topup
        if ($product->provider === 'manual_topup') {
            $data['submited'] = [
                ...$data['submited'],
                'gift_send' => false,
                'dispute' => false,
                'done' => false,
            ];
        }

        $data['provider'] = $product->provider;
        $data['provider_payload'] = $product->provider === 'h2h' ? [
            'productId' => $product->provider_product_id,
            'destination' => (string) $data['account_id'],
            'serverId' => (string) ($data['server_id'] ?? ''),
            'expectedPrice' => (int) $product->buy_price,
            'payment' => 'wallet', 'reference' => $data['reference'],
        ] : ['sku' => $product->sku];
        [$order, $payment] = DB::transaction(function () use ($data, $voucher, $discountAmount, $amountAfterDiscount) {
            $order = Order::create($data);

            // Record Voucher Usage
            if ($voucher && $discountAmount > 0) {
                VoucherUse::create([
                    'voucher_id' => $voucher->id,
                    'usable_type' => Order::class,
                    'usable_id' => $order->id,
                    'before_amount' => $data['amount'],
                    'discount_amount' => $discountAmount,
                    'after_amount' => $amountAfterDiscount,
                ]);

                $voucher->increment('used_count');
            }

            // Create payment record
            $orderId = uniqid().time();
            $payment = Payment::create([
                'driver' => $data['payment_type'] === 'automatic' ? $this->paymentGateway->driver() : 'manual',
                'payable_type' => Order::class,
                'payable_id' => $order->id,
                'order_id' => $orderId,
                'transaction_id' => null,
                'payment_type' => strtolower($data['payment_method']) === 'qris' ? 'qris' : 'bank_transfer',
                // AUTO_GENERATED
                'account_number' => 'AUTO_GENERATED',
                'channel' => $data['payment_method'],
                'expired_at' => now()->addHours(24),
                'amount' => $order->total_amount,
            ]);

            return [$order, $payment];
        });

        // Create a transaction with the configured payment gateway.
        if ($data['payment_type'] === 'automatic') {
            try {
                $this->paymentGateway->create($payment);
            } catch (\Throwable $e) {
                report($e);
                CreateGatewayPayment::dispatch($payment->id);
            }
            $order->refresh();
        } else {
            // For manual payment, we can set the account number to the payment method for easier reconciliation
            $payment->channel = getSetting('manual_transfer_bank');
            $payment->account_number = getSetting('manual_transfer_account_number');
            $payment->account_code = getSetting('manual_transfer_account_name');
            $payment->save();
        }

        // Send notification to user
        $message = getSetting('template_checkout') ?? '';
        $message = str_replace('{app_name}', config('app.name'), $message);
        $message = str_replace('{order_id}', $order->reference, $message);
        $message = str_replace('{product}', $product->name, $message);
        $message = str_replace('{total}', numberToCurrency($order->total_amount), $message);
        $message = str_replace('{link}', transactionUrl($order), $message);
        $message = str_replace('{cs_link}', getSetting('cs') ?? '', $message);

        // Send message via email
        Mail::to($order->email)->send(
            new OrderCreated($order)
        );

        $order->notifications()->create([
            'provider' => 'email',
            'title' => 'Order Created',
            'content' => $message,
            'error' => false,
        ]);

        return $order;
    }
}
