<?php

namespace App\Actions\Cms\Order\GiftOrder;

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
        // Upload file
        if ($data['admin_add_friend_proof'] ?? null instanceof UploadedFile) {
            $this->saveMedia(
                model: $order,
                file: $data['admin_add_friend_proof'],
                collection: 'admin_add_friend_proof',
            );
        }

        if ($data['user_confirm_friend_proof'] ?? null instanceof UploadedFile) {
            $this->saveMedia(
                model: $order,
                file: $data['user_confirm_friend_proof'],
                collection: 'user_confirm_friend_proof',
            );
        }

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
