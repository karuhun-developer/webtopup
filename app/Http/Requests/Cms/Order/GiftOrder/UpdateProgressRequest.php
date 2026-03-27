<?php

namespace App\Http\Requests\Cms\Order\GiftOrder;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProgressRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'admin_account_ign' => 'nullable|string|max:255',
            'admin_add_friend' => 'nullable|boolean',
            'admin_add_friend_timestamp' => 'nullable|date',
            'admin_add_friend_proof' => 'nullable|image|max:2048',
            'user_confirm_friend' => 'nullable|boolean',
            'user_confirm_friend_timestamp' => 'nullable|date',
            'user_confirm_friend_proof' => 'nullable|image|max:2048',
            'gift_send' => 'nullable|boolean',
            'gift_send_proof' => 'nullable|image|max:2048',
            'dispute' => 'nullable|boolean',
            'done' => 'nullable|boolean',
        ];
    }
}
