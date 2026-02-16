<?php

namespace App\Http\Controllers\Cms\Order;

use App\Actions\Cms\Order\GiftOrder\SendNotificationAction;
use App\Actions\Cms\Order\GiftOrder\UpdateProgressAction;
use App\Actions\Cms\Order\Order\ValidatePaymentAction;
use App\Actions\Main\StoreTransactionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Order\GiftOrder\SendNotificationRequest;
use App\Http\Requests\Cms\Order\GiftOrder\UpdateProgressRequest;
use App\Http\Requests\Cms\Order\Order\ValidatePaymentRequest;
use App\Http\Requests\Main\StoreTransactionRequest;
use App\Models\Account\Account;
use App\Models\Order\Order;
use App\Models\PPOB\PPOBProduct;
use App\Traits\WithGetFilterData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class GiftOrderController extends Controller
{
    use WithGetFilterData;

    protected string $resource = Order::class;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('view'.$this->resource);

        $order = $request?->order ?? 'desc';
        $orderBy = $request?->orderBy ?? 'created_at';
        $paginate = $request?->paginate ?? 10;
        $searchBySpecific = $request?->searchBySpecific ?? '';
        $search = $request?->search ?? '';
        $paymentStatusFilter = $request?->payment_status ?? [];
        $giftSendFilter = $request?->gift_send ?? [];

        $query = Order::with('brand', 'product', 'payment.media')->whereHas('product', fn ($q) => $q->where('provider', 'gift'));

        // Apply payment status filter
        if (! empty($paymentStatusFilter)) {
            $query->where(function ($q) use ($paymentStatusFilter) {
                foreach ($paymentStatusFilter as $status) {
                    if ($status === 'pending') {
                        $q->orWhere('payment_status', 0);
                    } elseif ($status === 'settlement') {
                        $q->orWhere('payment_status', 2);
                    } elseif ($status === 'failed') {
                        $q->orWhereIn('payment_status', [-1, -2]);
                    }
                }
            });
        }

        // Apply gift send filter
        if (! empty($giftSendFilter)) {
            $query->where(function ($q) use ($giftSendFilter) {
                foreach ($giftSendFilter as $status) {
                    $q->orWhere('submited->gift_send', (bool) $status);
                }
            });
        }

        $model = $this->getDataWithFilter(
            model: $query,
            searchBy: [
                'reference',
                'name',
                'email',
                'phone',
            ],
            order: $order,
            orderBy: $orderBy,
            paginate: $paginate,
            searchBySpecific: $searchBySpecific,
            s: $search,
        );

        // Load media
        $model->map(function ($item) {
            if ($item->payment->driver === 'manual') {
                $item->payment->image = $item->payment?->getFirstMediaUrl('image');
            }
            $item->payment->makeHidden('media');

            return $item;
        });

        return inertia('cms/order/gift-order/Index', [
            'data' => $model,
            'order' => $order,
            'orderBy' => $orderBy,
            'paginate' => $paginate,
            'searchBySpecific' => $searchBySpecific,
            'search' => $search,
            'resource' => $this->resource,
            'paymentStatusFilter' => $paymentStatusFilter,
            'giftSendFilter' => $giftSendFilter,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create'.$this->resource);

        $products = PPOBProduct::query()
            ->with('brand')
            ->where('provider', 'gift')
            ->get();

        return inertia('cms/order/gift-order/Create', [
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created resource in storage.
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
                    'submited',
                ]));
            });

            return back()->with('success', 'Order berhasil dibuat');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        Gate::authorize('view'.$this->resource);

        $order->load('brand', 'product', 'notifications', 'media');

        // Try to get Mobile Legends account nickname
        $mlAccount = null;
        if (isset($order->submited['account_id']) && isset($order->submited['server_id'])) {
            $mlAccount = Account::where('game', 'mobilelegends')
                ->where('uid', $order->submited['account_id'])
                ->where('server', $order->submited['server_id'])
                ->first();
        }

        // Load media admin_add_friend_proof user_confirm_friend_proof gift_send_proof
        $submittedData = $order->submited;
        $submittedData['admin_add_friend_proof'] = $order->getFirstMediaUrl('admin_add_friend_proof');
        $submittedData['user_confirm_friend_proof'] = $order->getFirstMediaUrl('user_confirm_friend_proof');
        $submittedData['gift_send_proof'] = $order->getFirstMediaUrl('gift_send_proof');
        $order->submited = $submittedData;

        return inertia('cms/order/gift-order/Show', [
            'order' => $order,
            'mlAccountNickname' => $mlAccount?->username,
        ]);
    }

    /**
     * Save order updates.
     */
    public function save(Order $order, UpdateProgressRequest $request, UpdateProgressAction $action)
    {
        Gate::authorize('update'.$this->resource);

        $action->handle($order, $request->validated());

        return back()->with('success', 'Progress pengiriman berhasil disimpan');
    }

    /**
     * Send Notification
     */
    public function notify(Order $order, SendNotificationRequest $request, SendNotificationAction $action)
    {
        Gate::authorize('update'.$this->resource);

        $action->handle($order, $request->validated());

        return back()->with('success', 'Notifikasi berhasil dikirim');
    }

    /**
     * Display validate payment modal.
     */
    public function validatePaymentView(Order $order)
    {
        Gate::authorize('update'.$this->resource);

        $order->load('brand', 'product', 'payment.media');

        // Load payment image if manual
        if ($order->payment && $order->payment->driver === 'manual') {
            $order->payment->image = $order->payment->getFirstMediaUrl('image');
        }

        $order->payment?->makeHidden('media');

        return inertia('cms/order/gift-order/ValidatePayment', [
            'order' => $order,
        ]);
    }

    /**
     * Validate payment for manual transfer.
     */
    public function validatePayment(Order $order, ValidatePaymentRequest $request, ValidatePaymentAction $action)
    {
        Gate::authorize('update'.$this->resource);

        $action->handle($order, $request->validated()['status']);

        return back()->with('success', 'Payment berhasil divalidasi');
    }
}
