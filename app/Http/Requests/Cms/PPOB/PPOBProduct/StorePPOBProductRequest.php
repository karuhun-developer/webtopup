<?php

namespace App\Http\Requests\Cms\PPOB\PPOBProduct;

use Illuminate\Foundation\Http\FormRequest;

class StorePPOBProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'p_p_o_b_brand_id' => 'required|exists:p_p_o_b_brands,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:p_p_o_b_products,sku',
            'description' => 'nullable|string|max:1000',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048', // max 2MB
            'status' => 'required|boolean',
        ];
    }
}
