<?php

namespace App\Actions\Cms\Order\Order;

use App\Models\Order\Order;

class ArchiveAllOrderAction
{
    public function handle(string|array $provider): void
    {
        Order::fromProviders((array) $provider)
            ->withoutArchive()
            ->update([
                'archive_at' => now(),
            ]);
    }
}
