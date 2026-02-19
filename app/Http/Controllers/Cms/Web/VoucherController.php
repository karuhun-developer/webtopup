<?php

namespace App\Http\Controllers\Cms\Web;

use App\Actions\Cms\Web\Voucher\DeleteVoucherAction;
use App\Actions\Cms\Web\Voucher\StoreVoucherAction;
use App\Actions\Cms\Web\Voucher\UpdateVoucherAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Web\Voucher\StoreVoucherRequest;
use App\Http\Requests\Cms\Web\Voucher\UpdateVoucherRequest;
use App\Models\Voucher\Voucher;
use App\Traits\WithGetFilterData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class VoucherController extends Controller
{
    use WithGetFilterData;

    protected string $resource = Voucher::class;

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

        $model = $this->getDataWithFilter(
            model: new Voucher,
            searchBy: [
                'code',
                'name',
                'description',
            ],
            order: $order,
            orderBy: $orderBy,
            paginate: $paginate,
            searchBySpecific: $searchBySpecific,
            s: $search,
        );

        return inertia('cms/web/voucher/Index', [
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create'.$this->resource);

        return inertia('cms/web/voucher/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVoucherRequest $request, StoreVoucherAction $action)
    {
        Gate::authorize('create'.$this->resource);

        $action->handle($request->validated());

        return to_route('cms.web.vouchers.index')->with('success', 'Voucher created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Voucher $voucher)
    {
        Gate::authorize('update'.$this->resource);

        return inertia('cms/web/voucher/Edit', [
            'voucher' => $voucher,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVoucherRequest $request, Voucher $voucher, UpdateVoucherAction $action)
    {
        Gate::authorize('update'.$this->resource);

        $action->handle($voucher, $request->validated());

        return to_route('cms.web.vouchers.index')->with('success', 'Voucher updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Voucher $voucher, DeleteVoucherAction $action)
    {
        Gate::authorize('delete'.$this->resource);

        $action->handle($voucher);

        return back()->with('success', 'Voucher deleted successfully.');
    }
}
