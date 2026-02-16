<?php

namespace App\Actions\Cms\Order\ManualTopup;

use App\Models\Order\Order;
use App\Traits\WithMediaCollection;
use Illuminate\Http\UploadedFile;

class UpdateProgressAction
{
    use WithMediaCollection;

    /**
     * Handle the action.
     */
    public function handle(Order $order, array $data): void
    {
        if ($data['gift_send_proof'] ?? null instanceof UploadedFile) {
            $this->saveMedia(
                model: $order,
                file: $data['gift_send_proof'],
                collection: 'gift_send_proof',
            );
        }

        // Update the order's submited field with the new data
        $order->update([
            'submited' => [
                ...$order->submited,
                ...$data,
            ],
        ]);
    }
}
