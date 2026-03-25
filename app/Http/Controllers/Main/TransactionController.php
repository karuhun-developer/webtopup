<?php

namespace App\Http\Controllers\Main;

use App\Actions\Main\CheckVoucherAction;
use App\Actions\Main\StoreTransactionAction;
use App\Actions\Main\UpdateTransactionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Main\CheckVoucherRequest;
use App\Http\Requests\Main\StoreTransactionRequest;
use App\Http\Requests\Main\UpdateTransactionRequest;
use App\Models\Account\Account;
use App\Models\Order\Order;
use App\Traits\WithReturnResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    use WithReturnResponse;

    /**
     * Display a listing of the resource.
     */
    public function check(Request $request)
    {
        // Search by reference if provided - redirect to show
        if ($request->has('reference')) {
            $order = Order::where('reference', $request->input('reference'))->first();

            // Check if order exists
            if (! $order) {
                return back()->withErrors(['reference' => 'Order not found with the provided reference.'])->withInput();
            }

            return to_route('transaction.show', [
                'order' => $order,
            ]);
        }

        $settingTitle = getSetting('title');
        $settingFavicon = getSetting('favicon') ?: '/favicon.svg';

        return inertia()->render('main/TransactionCheck', [
            'latestOrder' => Order::with('payment', 'product.media', 'brand.media')
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($order) {
                    // Map product image
                    if ($order->product) {
                        $order->product->image = $order->product->getFirstMediaUrl('image') ?: $order->brand?->getFirstMediaUrl('default_product_image');
                        $order->product->makeHidden('media');
                    }

                    // Map brand image
                    if ($order->brand) {
                        $order->brand->image = $order->brand->getFirstMediaUrl('image');
                        $order->brand->makeHidden('media');
                    }

                    $order->reference = preg_replace('/.(?=.{4})/', '*', $order->reference);
                    $order->total_amount = preg_replace('/.(?=.{3})/', '*', $order->total_amount);

                    return $this->maskData($order);
                }),
        ])->withViewData([
            'meta' => [
                'title' => 'Cek Transaksi - '.$settingTitle,
                'description' => 'Cek status transaksi top up game Anda di '.$settingTitle.'. Masukkan nomor transaksi untuk melihat detail pesanan Anda.',
                'keywords' => 'cek transaksi, status pesanan, lacak pesanan, '.$settingTitle.', topup game',
                'author' => $settingTitle,
                'application_name' => $settingTitle,
                'url' => route('transaction.check'),
                'image' => config('app.url').$settingFavicon,
            ],
        ]);
    }

    /**
     * Display the specified transaction.
     */
    public function show(Order $order)
    {
        $order->load('payment.media', 'product.media', 'brand.media', 'product.brand.category', 'media');

        // Map product image
        if ($order->product) {
            $order->product->image = $order->product->getFirstMediaUrl('image') ?: $order->brand?->getFirstMediaUrl('default_product_image');
            $order->product->makeHidden('media');
        }

        // Map brand image
        if ($order->brand) {
            $order->brand->image = $order->brand->getFirstMediaUrl('image');
            $order->brand->makeHidden('media');
        }

        // Payment proof image for manual payment
        if ($order->payment->driver === 'manual') {
            $order->payment->image = $order->payment?->getFirstMediaUrl('image');
            $order->payment->makeHidden('media');
        }

        // Load media specific for gift/manual topup orders
        if ($order->product) {
            $submittedData = $order->submited;

            // Check if it's a gift order
            if ($order->product->provider === 'gift') {
                $submittedData['admin_add_friend_proof'] = $order->getFirstMediaUrl('admin_add_friend_proof');
                $submittedData['user_confirm_friend_proof'] = $order->getFirstMediaUrl('user_confirm_friend_proof');
                $submittedData['gift_send_proof'] = $order->getFirstMediaUrl('gift_send_proof');
            }

            // Check if it's a manual topup order
            elseif ($order->product->provider === 'manual_topup') {
                $submittedData['gift_send_proof'] = $order->getFirstMediaUrl('gift_send_proof');
            }

            $order->submited = $submittedData;
        }

        // Try to get Mobile Legends account nickname
        $mlAccount = null;
        if (isset($order->submited['account_id']) && isset($order->submited['server_id'])) {
            $mlAccount = Account::where('game', 'mobilelegend')
                ->where('uid', $order->submited['account_id'])
                ->where('server', $order->submited['server_id'])
                ->first();
        }

        $order = $this->maskData($order);

        return inertia('main/TransactionShow', [
            'order' => $order,
            'mlAccountNickname' => $mlAccount->username ?? null,
        ]);
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(StoreTransactionRequest $request, StoreTransactionAction $action)
    {
        try {
            $order = DB::transaction(function () use ($request, $action) {
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
                    'voucher_code',
                    'submited',
                ]));
            });

            return to_route('transaction.show', [
                'order' => $order,
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Update payment proof for manual payment
     */
    public function update(UpdateTransactionRequest $request, Order $order, UpdateTransactionAction $action)
    {
        $action->handle($order, $request->validated());

        return back();
    }

    /**
     * Check voucher validity
     */
    public function checkVoucher(CheckVoucherRequest $request, CheckVoucherAction $action)
    {
        $result = $action->handle($request->validated());

        if (! $result['valid']) {
            return $this->responseWithError($result['message'], 422);
        }

        return $this->responseWithSuccess($result);
    }

    /**
     * Remove the specified resource from storage.
     */
    private function maskData(Order $order)
    {
        // Mask sensitive data
        $order->name = preg_replace('/.(?=.{2})/', '*', $order->name);
        $order->email = preg_replace('/(?<=.).(?=[^@]*?.@)/', '*', $order->email);
        $order->phone = preg_replace('/.(?=.{4})/', '*', $order->phone);

        if (isset($order->submited['account_id'])) {
            $account_id = preg_replace('/.(?=.{2})/', '*', $order->submited['account_id']);
            $order->submited = [
                ...$order->submited,
                'account_id' => $account_id,
            ];
        }

        if (isset($order->submited['server_id'])) {
            $server_id = preg_replace('/.(?=.{2})/', '*', $order->submited['server_id']);
            $order->submited = [
                ...$order->submited,
                'server_id' => $server_id,
            ];
        }

        return $order;
    }
}
