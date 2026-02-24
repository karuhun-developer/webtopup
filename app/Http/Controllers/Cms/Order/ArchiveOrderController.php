<?php

namespace App\Http\Controllers\Cms\Order;

use App\Actions\Cms\Order\Order\UnarchiveAllOrderAction;
use App\Actions\Cms\Order\Order\UnarchiveOrderAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Order\Order\BulkOrderRequest;
use App\Models\Order\Order;
use App\Traits\WithGetFilterData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ArchiveOrderController extends Controller
{
    use WithGetFilterData;

    protected string $resource = Order::class;

    /**
     * Display a listing of archived orders.
     */
    public function index(Request $request)
    {
        Gate::authorize('view'.$this->resource);

        $order = $request?->order ?? 'desc';
        $orderBy = $request?->orderBy ?? 'created_at';
        $paginate = $request?->paginate ?? 10;
        $searchBySpecific = $request?->searchBySpecific ?? '';
        $search = $request?->search ?? '';

        $query = Order::with('brand', 'product', 'payment.media')->onlyArchive();

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

        return inertia('cms/order/archive/Index', [
            'data' => $model,
            'order' => $order,
            'orderBy' => $orderBy,
            'paginate' => $paginate,
            'searchBySpecific' => $searchBySpecific,
            'search' => $search,
            'resource' => $this->resource,
        ]);
    }

    /**
     * Unarchive selected orders.
     */
    public function unarchive(BulkOrderRequest $request, UnarchiveOrderAction $action)
    {
        Gate::authorize('update'.$this->resource);

        $action->handle($request->ids);

        return back()->with('success', 'Orders unarchived successfully');
    }

    /**
     * Unarchive all orders.
     */
    public function unarchiveAll(Request $request, UnarchiveAllOrderAction $action)
    {
        Gate::authorize('update'.$this->resource);

        $action->handle();

        return back()->with('success', 'All orders unarchived successfully');
    }
}
