<?php

namespace App\Actions\Api\V1\Auth;

class ResendAuthenticatedAction
{
    /**
     * Handle the action.
     */
    public function handle(): bool
    {
        $user = auth()->user();

        if ($user->hasVerifiedEmail()) {
            return false;
        }

        $user->sendEmailVerificationNotification();

        // Save activity
        activity()->performedOn($user)->causedBy($user)->log('Resend Verification Email');

        return true;
    }
}
