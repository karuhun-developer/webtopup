<?php

namespace App\Actions\Api\V1\Auth;

class StoreAuthenticatedAction
{
    /**
     * Handle the action.
     */
    public function handle(array $data): bool
    {
        if (! auth()->attempt($data)) {
            return false;
        }

        // Save activity
        activity()->performedOn(auth()->user())->causedBy(auth()->user())->event('Login')->log('Login');

        // Delete all previous tokens
        // auth()->user()->tokens()->delete();

        return true;
    }
}
