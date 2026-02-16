<?php

namespace App\Http\Requests\Cms\Order\ManualTopup;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProgressRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'gift_send' => 'nullable|boolean',
            'gift_send_proof' => 'nullable|image|max:2048',
            'dispute' => 'nullable|boolean',
            'done' => 'nullable|boolean',
        ];
    }
}
