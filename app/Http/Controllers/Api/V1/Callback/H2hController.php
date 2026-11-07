<?php

namespace App\Http\Controllers\Api\V1\Callback;

use App\Actions\Api\V1\Callback\HandleH2hCallbackAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class H2hController extends Controller
{
    public function callback(Request $request, HandleH2hCallbackAction $action): JsonResponse
    {
        try {
            $action->handle($request->getContent(), $request->header('X-H2H-Timestamp', ''),
                $request->header('X-H2H-Signature', ''), $request->header('X-H2H-Event', ''));
        } catch (\RuntimeException $e) {
            if (in_array($e->getCode(), [403, 422], true)) {
                abort($e->getCode(), $e->getMessage());
            }
            throw $e;
        }

        return response()->json(['status' => true]);
    }
}
