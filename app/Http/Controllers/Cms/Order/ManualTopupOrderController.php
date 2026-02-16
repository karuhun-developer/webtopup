<?php

namespace App\Http\Controllers\Cms\Order;

use App\Actions\Cms\Order\ManualTopup\SendNotificationAction;
use App\Actions\Cms\Order\ManualTopup\UpdateProgressAction;
use App\Actions\Cms\Order\Order\StoreOrderAction;
use App\Actions\Cms\Order\Order\ValidatePaymentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Order\ManualTopup\SendNotificationRequest;
use App\Http\Requests\Cms\Order\ManualTopup\UpdateProgressRequest;
use App\Http\Requests\Cms\Order\Order\StoreOrderRequest;
use App\Http\Requests\Cms\Order\Order\ValidatePaymentRequest;
use App\Models\Account\Account;
use App\Models\Order\Order;
use App\Traits\WithGetFilterData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ManualTopupOrderController extends Controller
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

        $query = Order::with('brand', 'product', 'payment.media')->whereHas('product', fn ($q) => $q->where('provider', 'manual_topup'));

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

        return inertia('cms/order/manual-topup/Index', [
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

        return inertia('cms/order/manual-topup/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request, StoreOrderAction $action)
    {
        Gate::authorize('create'.$this->resource);

        $action->handle($request->validated());

        return back();
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

        // Load media gift_send_proof
        $submittedData = $order->submited;
        $submittedData['gift_send_proof'] = $order->getFirstMediaUrl('gift_send_proof');
        $order->submited = $submittedData;

        return inertia('cms/order/manual-topup/Show', [
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
}
