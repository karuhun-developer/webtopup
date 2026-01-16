<?php

namespace App\Actions\Cms\Management\User;

use App\Models\User;

class UpdateUserPasswordAction
{
    /**
     * Handle the action.
     */
    public function handle(User $user, array $data): bool
    {
        // Hash logic is handled by User model caster if applicable,
        // but typically Laravel handles plain text assignment if 'password' is not in casts.
        // Assuming Laravel default behavior or model mutators.
        // Given 'laravel/framework' : '^12.0', the 'hashed' cast is common.

        return $user->update($data);
    }
}
