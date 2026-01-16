<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Actions\Api\V1\Auth\StoreRegisterAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\StoreRegisterRequest;

class RegisterController extends Controller
{
    /**
     * Handle the incoming registration request.
     */
    public function store(StoreRegisterRequest $request, StoreRegisterAction $action)
    {
        $user = $action->handle($request->validated());

        return $this->responseWithCreated([
            'token' => $user->createToken('API Token')->plainTextToken,
            'token_type' => 'bearer',
        ]);
    }
}
