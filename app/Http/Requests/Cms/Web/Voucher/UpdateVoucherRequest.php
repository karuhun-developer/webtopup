<?php

namespace App\Http\Requests\Cms\Web\Voucher;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVoucherRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => 'required|string|max:255|unique:vouchers,code,'.$this->voucher->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:FIXED_AMOUNT,PERCENTAGE',
            'fixed_amount' => 'nullable',
            'percentage' => 'nullable|numeric|between:0,100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'min_purchase_amount' => 'nullable',
            'usage_limit' => 'nullable',
            'status' => 'boolean',
        ];
    }
}
