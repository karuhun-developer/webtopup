<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAuthenticatedRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.auth()->id(),
            'phone' => 'nullable|unique:users,phone,'.auth()->id(),
            'password' => 'nullable|string|min:8|confirmed',
            'image' => 'nullable|image|max:5120', // max 5MB
        ];
    }
}
