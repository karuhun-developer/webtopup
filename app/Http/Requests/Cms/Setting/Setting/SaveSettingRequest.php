<?php

namespace App\Http\Requests\Cms\Setting\Setting;

use Illuminate\Foundation\Http\FormRequest;

class SaveSettingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'value' => 'nullable|array',
            'value.title' => 'nullable|string',
            'value.email' => 'nullable|email',
            'value.phone' => 'nullable|string',
            'value.privacy_policy' => 'nullable|string',
            'value.terms' => 'nullable|string',
            'value.footer_description' => 'nullable|string',
            'value.cs' => 'nullable|string',
            'value.logo' => 'nullable|image:allow_svg|max:2048',
            'value.icon' => 'nullable|image:allow_svg|max:2048',
            'value.favicon' => 'nullable|image:allow_svg|max:1024',
            'value.template_checkout' => 'nullable|string',
            'value.template_payment_confirmation' => 'nullable|string',
            'value.template_payment_rejected' => 'nullable|string',
            'value.template_order_completed' => 'nullable|string',
            'value.template_order_failed' => 'nullable|string',
            'value.template_gift_order_admin_friend_request' => 'nullable|string',
            'value.template_gift_order_user_friend_confirmation' => 'nullable|string',
            'value.template_gift_order_completion' => 'nullable|string',
            'value.manual_transfer_bank' => 'nullable|string',
            'value.manual_transfer_bank_logo' => 'nullable|image:allow_svg|max:2048',
            'value.manual_transfer_account_name' => 'nullable|string',
            'value.manual_transfer_account_number' => 'nullable|string',
            'value.manual_transfer_type' => 'nullable|in:rekening,qris',
            'value.maintenance_status' => 'nullable|in:active,inactive',
            'value.maintenance_title' => 'nullable|string',
            'value.maintenance_description' => 'nullable|string',
            'value.maintenance_image' => 'nullable|image:allow_svg|max:2048',
        ];
    }
}
