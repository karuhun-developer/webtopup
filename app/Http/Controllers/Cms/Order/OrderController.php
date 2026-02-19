<?php

namespace App\Http\Controllers\Cms\Order;

use App\Actions\Cms\Order\Order\StoreOrderAction;
use App\Actions\Cms\Order\Order\ValidatePaymentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Order\Order\StoreOrderRequest;
use App\Http\Requests\Cms\Order\Order\ValidatePaymentRequest;
use App\Models\Order\Order;
use App\Traits\WithGetFilterData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
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
        $topupStatusFilter = $request?->topup_status ?? [];

        $query = Order::with('brand', 'product', 'payment.media')->whereHas('brand', fn ($q) => $q->where('provider', 'digiflazz'));

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

        // Apply topup status filter
        if (! empty($topupStatusFilter)) {
            $query->whereIn('topup_status', $topupStatusFilter);
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
            if ($item->payment?->driver === 'manual') {
                $item->payment->image = $item->payment?->getFirstMediaUrl('image');
            }
            $item->payment?->makeHidden('media');

            return $item;
        });

        return inertia('cms/order/order/Index', [
            'data' => $model,
            'order' => $order,
            'orderBy' => $orderBy,
            'paginate' => $paginate,
            'searchBySpecific' => $searchBySpecific,
            'search' => $search,
            'resource' => $this->resource,
            'paymentStatusFilter' => $paymentStatusFilter,
            'topupStatusFilter' => $topupStatusFilter,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create'.$this->resource);

        return inertia('cms/order/order/Create');
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

        $order->load('brand', 'product', 'payment.media', 'voucherUse.voucher');

        // Load payment image if manual
        if ($order->payment && $order->payment->driver === 'manual') {
            $order->payment->image = $order->payment->getFirstMediaUrl('image');
        }

        $order->payment?->makeHidden('media');

        return inertia('cms/order/order/Show', [
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
