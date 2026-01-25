<?php

namespace App\Http\Controllers\Cms\PPOB;

use App\Actions\Cms\PPOB\PPOBProduct\DeletePPOBProductAction;
use App\Actions\Cms\PPOB\PPOBProduct\StorePPOBProductAction;
use App\Actions\Cms\PPOB\PPOBProduct\UpdatePPOBProductAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\PPOB\PPOBProduct\StorePPOBProductRequest;
use App\Http\Requests\Cms\PPOB\PPOBProduct\UpdatePPOBProductRequest;
use App\Models\PPOB\PPOBBrand;
use App\Models\PPOB\PPOBCategory;
use App\Models\PPOB\PPOBProduct;
use App\Traits\WithGetFilterData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PPOBProductController extends Controller
{
    use WithGetFilterData;

    protected string $resource = PPOBProduct::class;

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
            model: PPOBProduct::with('media', 'brand.category')->when($request->filter_category_id, function ($query) use ($request) {
                $query->whereHas('brand', function ($q) use ($request) {
                    $q->where('p_p_o_b_category_id', $request->filter_category_id);
                });
            })->when($request->filter_brand_id, function ($query) use ($request) {
                $query->where('p_p_o_b_brand_id', $request->filter_brand_id);
            }),
            searchBy: [
                'name',
                'sku',
                'description',
                'buy_price',
                'sell_price',
            ],
            order: $order,
            orderBy: $orderBy,
            paginate: $paginate,
            searchBySpecific: $searchBySpecific,
            s: $search,
        );

        // Load media
        $model->map(function ($item) {
            $item->image = $item->getFirstMediaUrl('image');

            return $item;
        });

        return inertia('cms/ppob/ppob-product/Index', [
            'categories' => PPOBCategory::where('status', true)->get(),
            'brands' => PPOBBrand::where('status', true)->where('p_p_o_b_category_id', $request->filter_category_id)->get(),
            'filter_category_id' => (int) $request->filter_category_id ?? null,
            'filter_brand_id' => (int) $request->filter_brand_id ?? null,
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

        return inertia('cms/ppob/ppob-product/Create', [
            'categories' => PPOBCategory::where('status', true)->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePPOBProductRequest $request, StorePPOBProductAction $action)
    {
        Gate::authorize('create'.$this->resource);

        $action->handle($request->validated());

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(PPOBProduct $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PPOBProduct $product)
    {
        Gate::authorize('update'.$this->resource);

        $product->image = $product->getFirstMediaUrl('image');

        return inertia('cms/ppob/ppob-product/Edit', [
            'categories' => PPOBCategory::where('status', true)->get(),
            'product' => $product->load('brand'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePPOBProductRequest $request, PPOBProduct $product, UpdatePPOBProductAction $action)
    {
        Gate::authorize('update'.$this->resource);

        $action->handle($product, $request->validated());

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PPOBProduct $product, DeletePPOBProductAction $action)
    {
        Gate::authorize('delete'.$this->resource);

        $action->handle($product);

        return back();
    }
}
