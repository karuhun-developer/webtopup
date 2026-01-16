<?php

namespace App\Actions\Cms\Management\User;

use App\Models\User;

class StoreUserAction
{
    /**
     * Handle the action.
     */
    public function handle(array $data): User
    {
        $user = User::create($data);

        if (isset($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        return $user;
    }
}
