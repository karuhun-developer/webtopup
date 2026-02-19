<?php

namespace App\Actions\Cms\Web\Voucher;

use App\Models\Voucher\Voucher;

class StoreVoucherAction
{
    /**
     * Handle the action.
     */
    public function handle(array $data): Voucher
    {
        if (isset($data['fixed_amount'])) {
            $data['fixed_amount'] = currencyToNumber($data['fixed_amount']);
        }

        if (isset($data['min_purchase_amount'])) {
            $data['min_purchase_amount'] = currencyToNumber($data['min_purchase_amount']);
        }

        if (isset($data['usage_limit'])) {
            $data['usage_limit'] = currencyToNumber($data['usage_limit']);
        }

        return Voucher::create($data);
    }
}
