<?php

namespace App\Http\Controllers\Api\V1\Callback;

use App\Actions\Api\V1\Callback\HandleMidtransCallbackAction;
use App\Http\Controllers\Controller;
use App\Traits\WithReturnResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    use WithReturnResponse;

    public function callback(Request $request, HandleMidtransCallbackAction $action)
    {
        try {
            $action->handle($request->all());
        } catch (\Exception $e) {
            Log::error('Midtrans Callback Error', [
                'error' => $e->getMessage(),
            ]);
            return $this->responseWithError($e->getMessage(), 400);
        }

        return $this->responseWithSuccess('Callback received');
    }
}
