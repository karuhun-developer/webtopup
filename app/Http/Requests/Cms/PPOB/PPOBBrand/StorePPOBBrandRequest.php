<?php

namespace App\Http\Requests\Cms\PPOB\PPOBBrand;

use Illuminate\Foundation\Http\FormRequest;

class StorePPOBBrandRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'p_p_o_b_category_id' => 'required|exists:p_p_o_b_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:2048', // max 2MB
            'status' => 'required|boolean',
        ];
    }
}
