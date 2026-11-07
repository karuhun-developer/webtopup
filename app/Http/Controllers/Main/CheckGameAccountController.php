<?php

namespace App\Http\Controllers\Main;

use App\Actions\Main\CheckGameAccountAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Main\CheckGameAccountRequest;
use Illuminate\Http\JsonResponse;

class CheckGameAccountController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function check(CheckGameAccountRequest $request, CheckGameAccountAction $action): JsonResponse
    {
        $result = $action->handle($request->validated());

        if (! $result['status']) {
            return $this->responseWithError($result['message'] ?? 'Game ID invalid', 422);
        }

        return $this->responseWithSuccess($result['data']);
    }
}
