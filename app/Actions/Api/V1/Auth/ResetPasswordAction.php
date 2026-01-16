<?php

namespace App\Actions\Api\V1\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Password;

class ResetPasswordAction
{
    /**
     * Handle the action.
     */
    public function handle(array $data): bool
    {
        $user = User::where('email', $data['email'])->first();
        $status = Password::sendResetLink(
            $user->email
        );

        // Save activity
        activity()->performedOn($user)->causedBy($user)->event('Reset Password')->log('Reset Password');

        return $status === Password::RESET_LINK_SENT;
    }
}
