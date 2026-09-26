<?php

namespace App\Http\Requests\Cms\Management\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UpdateUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'role' => ['required', 'exists:roles,name', Rule::in($this->assignableRoles())],
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$this->user->id,
            'phone' => 'nullable|string|max:20|unique:users,phone,'.$this->user->id,
        ];
    }

    /**
     * Roles the authenticated actor is allowed to assign.
     *
     * @return array<int, string>
     */
    private function assignableRoles(): array
    {
        if ($this->user()?->hasRole('superadmin')) {
            return Role::pluck('name')->all();
        }

        return ['admin', 'user'];
    }
}
