<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Actions\Api\V1\Auth\ResendAuthenticatedAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\ResetPasswordRequest;

class PasswordResetController extends Controller
{
    /**
     * Handle the incoming password reset request.
     */
    public function store(ResetPasswordRequest $request, ResendAuthenticatedAction $action)
    {
        if (! $action->handle($request->validated())) {
            return $this->responseWithError('Unable to resend verification email.', 422);
        }

        return $this->responseWithSuccess('Password reset link sent successfully.');
    }
}
