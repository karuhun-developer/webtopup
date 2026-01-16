<?php

namespace App\Actions\Cms\Management\User;

use App\Models\User;

class ValidateUserEmailAction
{
    /**
     * Handle the action.
     */
    public function handle(User $user): bool
    {
        return $user->markEmailAsVerified();
    }
}
