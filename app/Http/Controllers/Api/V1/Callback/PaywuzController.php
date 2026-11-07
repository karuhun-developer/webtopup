<?php

namespace App\Http\Controllers\Api\V1\Callback;

use App\Actions\Api\V1\Callback\HandlePaywuzCallbackAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaywuzController extends Controller
{
    public function callback(Request $request, HandlePaywuzCallbackAction $action): JsonResponse
    {
        try {
            $action->handle($request->getContent(), $request->header('X-Paywuz-Signature', ''));
        } catch (\RuntimeException $e) {
            if (in_array($e->getCode(), [403, 422], true)) {
                abort($e->getCode(), $e->getMessage());
            }
            throw $e;
        }

        return response()->json(['status' => true]);
    }
}
