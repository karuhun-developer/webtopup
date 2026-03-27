<?php

namespace App\Http\Requests\Cms\PPOB\PPOBBrand;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePPOBBrandRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'p_p_o_b_category_id' => 'required|exists:p_p_o_b_categories,id',
            'name' => 'required|string|max:255',
            'provider' => 'required|in:digiflazz,gift,manual_topup',
            'description' => 'nullable|string|max:1000',
            'featured' => 'required|boolean',
            'order' => 'required|integer|min:1',
            'settings' => 'nullable|array',
            'settings.type' => 'required_with:settings|string|in:id,id+server',
            'settings.label_id' => 'required_with:settings|string|max:255',
            'settings.label_server' => 'required_if:settings.type,id+server|string|max:255',
            'settings.servers' => 'nullable|array',
            'image' => 'nullable|image|max:5120', // max 5MB
            'banner' => 'nullable|image|max:5120', // max 5MB
            'default_product_image' => 'nullable|image|max:2048', // max 2MB
            'status' => 'required|boolean',
        ];
    }
}
