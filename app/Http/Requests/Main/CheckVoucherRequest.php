<?php

namespace App\Http\Requests\Main;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CheckVoucherRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'voucher_code' => 'required|string|exists:vouchers,code',
            'amount' => 'required|numeric|min:0',
        ];
    }
}
