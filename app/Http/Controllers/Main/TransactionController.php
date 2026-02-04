<?php

namespace App\Http\Controllers\Main;

use App\Actions\Main\StoreTransactionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Main\StoreTransactionRequest;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function check()
    {
        return inertia('main/TransactionCheck');
    }

    public function store(StoreTransactionRequest $request, StoreTransactionAction $action)
    {
        try {
            $transaction = DB::transaction(function () use ($request, $action) {
                return $action->handle($request->only([
                    'type',
                    'account_id',
                    'server_id',
                    'product_id',
                    'email',
                    'name',
                    'phone',
                    'payment_type',
                    'payment_method',
                    'p_p_o_b_brand_id',
                    'p_p_o_b_product_id',
                    'submited',
                ]));
            });

            return to_route('transaction.check');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}
