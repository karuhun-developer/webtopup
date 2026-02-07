<?php

namespace App\Http\Requests\Cms\Order\Order;

use App\Enums\PaymentStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class ValidatePaymentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:'.PaymentStatusEnum::SETTLEMENT->value.','.PaymentStatusEnum::DENY->value,
        ];
    }
}
