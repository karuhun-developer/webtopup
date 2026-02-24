<?php

namespace App\Actions\Cms\Order\Order;

use App\Models\Order\Order;

class UnarchiveOrderAction
{
    public function handle(array $ids): void
    {
        if (empty($ids)) {
            return;
        }

        Order::whereIn('id', $ids)
            ->update([
                'archive_at' => null,
            ]);
    }
}
