<?php

namespace App\Actions\Cms\Order\Order;

use App\Models\Order\Order;

class ArchiveAllOrderAction
{
    public function handle(string $provider): void
    {
        Order::whereHas('product', fn ($q) => $q->where('provider', $provider))
            ->withoutArchive()
            ->update([
                'archive_at' => now(),
            ]);
    }
}
