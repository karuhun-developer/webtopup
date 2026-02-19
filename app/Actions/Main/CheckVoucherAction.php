<?php

namespace App\Actions\Main;

use App\Models\Voucher\Voucher;
class CheckVoucherAction
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function handle(array $data)
    {
        $voucher = Voucher::where('code', $data['voucher_code'])->first();

        // Check active status
        if (! $voucher->status) {
            return [
                'valid' => false,
                'message' => 'Voucher is inactive.',
            ];
        }

        // Check date range
        $now = now();
        if ($voucher->start_date && $now->lt($voucher->start_date)) {
            return [
                'valid' => false,
                'message' => 'Voucher is not yet active.',
            ];
        }
        if ($voucher->end_date && $now->gt($voucher->end_date)) {
            return [
                'valid' => false,
                'message' => 'Voucher has expired.',
            ];
        }

        // Check usage limit
        if ($voucher->usage_limit > 0 && $voucher->used_count >= $voucher->usage_limit) {
            return [
                'valid' => false,
                'message' => 'Voucher usage limit reached.',
            ];
        }

        // Check minimum purchase amount
        if ($voucher->min_purchase_amount > 0 && $data['amount'] < $voucher->min_purchase_amount) {
            return [
                'valid' => false,
                'message' => 'Minimum purchase amount not met. Min: '.number_format($voucher->min_purchase_amount),
            ];
        }

        // Calculate discount
        $discount = 0;
        if ($voucher->type === 'FIXED_AMOUNT') {
            $discount = $voucher->fixed_amount;
        } elseif ($voucher->type === 'PERCENTAGE') {
            $discount = ($data['amount'] * $voucher->percentage) / 100;
        }

        // Ensure discount doesn't exceed transaction amount
        $discount = min($discount, $data['amount']);

        return [
            'valid' => true,
            'discount_amount' => $discount,
            'voucher_code' => $voucher->code,
            'type' => $voucher->type,
        ];
    }
}
