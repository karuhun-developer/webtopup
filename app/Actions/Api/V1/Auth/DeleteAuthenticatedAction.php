<?php

namespace App\Actions\Api\V1\Auth;

class DeleteAuthenticatedAction
{
    /**
     * Handle the action.
     */
    public function handle(): void
    {
        // Save activity
        activity()->performedOn(auth()->user())->causedBy(auth()->user())->event('Login')->log('Logout');

        auth()->user()->tokens()->delete();
    }
}
