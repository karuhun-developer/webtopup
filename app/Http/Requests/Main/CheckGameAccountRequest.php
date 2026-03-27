<?php

namespace App\Http\Requests\Main;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CheckGameAccountRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required',
            'server_id' => 'nullable',
            'product_id' => 'nullable|exists:p_p_o_b_products,id',
            'slug' => 'required_without:product_id|exists:p_p_o_b_brands,slug',
        ];
    }
}
