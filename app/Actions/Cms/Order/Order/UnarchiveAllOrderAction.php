<?php

namespace App\Actions\Cms\Order\Order;

use App\Models\Order\Order;

class UnarchiveAllOrderAction
{
    public function handle(): void
    {
        Order::query()->onlyArchive()
            ->update([
                'archive_at' => null,
            ]);
    }
}
