<?php

namespace App\Http\Requests\Cms\Management\Menu;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'role_id' => 'required|exists:roles,id',
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'url' => 'required|string|max:255',
            'order' => 'required|integer',
            'active_pattern' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ];
    }
}
