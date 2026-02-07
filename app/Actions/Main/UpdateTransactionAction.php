<?php

namespace App\Actions\Main;

use App\Models\Order\Order;
use App\Traits\WithMediaCollection;

class UpdateTransactionAction
{
    use WithMediaCollection;

    /**
     * Handle the action.
     */
    public function handle(Order $order, array $data): void
    {
        $this->saveMedia(
            model: $order->payment,
            file: $data['image'],
            collection: 'image',
        );
    }
}
