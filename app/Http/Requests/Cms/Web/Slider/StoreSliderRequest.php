<?php

namespace App\Http\Requests\Cms\Web\Slider;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSliderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'order' => 'required|integer|min:1',
            'link' => 'nullable|url|max:255',
            'image' => 'nullable|image|max:2048', // max 2MB
            'status' => 'required|boolean',
        ];
    }
}
