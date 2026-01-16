<?php

namespace App\Actions\Cms\Management\User;

use App\Models\User;

class DeleteUserAction
{
    /**
     * Handle the action.
     */
    public function handle(User $user): ?bool
    {
        return $user->delete();
    }
}
