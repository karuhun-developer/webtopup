<?php

namespace App\Http\Controllers\Api\V1\Callback;

use App\Actions\Api\V1\Callback\HandleDigiflazzCallbackAction;
use App\Http\Controllers\Controller;
use App\Traits\WithReturnResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DigiflazzController extends Controller
{
    use WithReturnResponse;

    public function callback(Request $request, HandleDigiflazzCallbackAction $action)
    {
        try {
            // Get signature and payload
            $signature = $request->header('X-Hub-Signature');
            $payload = $request->getContent();
            $data = $request->input('data');

            $action->handle($signature, $payload, $data);
        } catch (\Exception $e) {
            Log::error('Digiflazz Callback Error', [
                'error' => $e->getMessage(),
            ]);

            return $this->responseWithError($e->getMessage(), 400);
        }

        return $this->responseWithSuccess('Callback received');
    }
}
