<?php

namespace App\Actions\Cms\Web\Voucher;

use App\Models\Voucher\Voucher;

class DeleteVoucherAction
{
    /**
     * Handle the action.
     */
    public function handle(Voucher $voucher): bool
    {
        return $voucher->delete();
    }
}
