<?php

namespace App\Http\Requests\Cms\PPOB\PPOBProduct;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePPOBProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'p_p_o_b_brand_id' => 'required|exists:p_p_o_b_brands,id',
            'name' => 'required|string|max:255',
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('p_p_o_b_products', 'sku')->where('provider', $this->input('provider'))->ignore($this->product)],
            'provider' => 'required|in:h2h,digiflazz,gift,manual_topup',
            'provider_product_id' => 'nullable|required_if:provider,h2h|string|max:255',
            'description' => 'nullable|string|max:1000',
            'delay' => 'required|boolean',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048', // max 2MB
            'status' => 'required|boolean',
        ];
    }
}
