<?php

namespace App\Http\Requests\Cms\PPOB;

use Illuminate\Foundation\Http\FormRequest;

class ImportProductRequest extends FormRequest
{
    public function rules(): array
    {
        return ['provider' => 'required|in:h2h,digiflazz'];
    }
}
