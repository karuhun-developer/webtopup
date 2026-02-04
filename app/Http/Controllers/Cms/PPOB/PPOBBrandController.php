<?php

namespace App\Http\Controllers\Cms\PPOB;

use App\Actions\Cms\PPOB\PPOBBrand\DeletePPOBBrandAction;
use App\Actions\Cms\PPOB\PPOBBrand\StorePPOBBrandAction;
use App\Actions\Cms\PPOB\PPOBBrand\UpdatePPOBBrandAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\PPOB\PPOBBrand\StorePPOBBrandRequest;
use App\Http\Requests\Cms\PPOB\PPOBBrand\UpdatePPOBBrandRequest;
use App\Models\PPOB\PPOBBrand;
use App\Models\PPOB\PPOBCategory;
use App\Traits\WithGetFilterData;
use App\Traits\WithReturnResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PPOBBrandController extends Controller
{
    use WithGetFilterData, WithReturnResponse;

    protected string $resource = PPOBBrand::class;

    /**
     * Display all json the resource.
     */
    public function jsonAll(Request $request)
    {
        return $this->responseWithSuccess(PPOBBrand::where('status', true)->where('p_p_o_b_category_id', $request->category_id)->get());
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('view'.$this->resource);

        $order = $request?->order ?? 'asc';
        $orderBy = $request?->orderBy ?? 'order';
        $paginate = $request?->paginate ?? 10;
        $searchBySpecific = $request?->searchBySpecific ?? '';
        $search = $request?->search ?? '';

        $model = $this->getDataWithFilter(
            model: PPOBBrand::with('media', 'category')->when($request->has('filter_category_id'), function ($query) use ($request) {
                $query->where('p_p_o_b_category_id', $request->filter_category_id);
            }),
            searchBy: [
                'name',
                'description',
                'order',
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

        return inertia('cms/ppob/ppob-brand/Index', [
            'categories' => PPOBCategory::where('status', true)->get(),
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

        return inertia('cms/ppob/ppob-brand/Create', [
            'categories' => PPOBCategory::where('status', true)->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePPOBBrandRequest $request, StorePPOBBrandAction $action)
    {
        Gate::authorize('create'.$this->resource);

        $action->handle($request->validated());

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(PPOBBrand $brand)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PPOBBrand $brand)
    {
        Gate::authorize('update'.$this->resource);

        $brand->image = $brand->getFirstMediaUrl('image');
        $brand->banner = $brand->getFirstMediaUrl('banner');
        $brand->default_product_image = $brand->getFirstMediaUrl('default_product_image');

        return inertia('cms/ppob/ppob-brand/Edit', [
            'categories' => PPOBCategory::where('status', true)->get(),
            'brand' => $brand,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePPOBBrandRequest $request, PPOBBrand $brand, UpdatePPOBBrandAction $action)
    {
        Gate::authorize('update'.$this->resource);

        $action->handle($brand, $request->validated());

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PPOBBrand $brand, DeletePPOBBrandAction $action)
    {
        Gate::authorize('delete'.$this->resource);

        $action->handle($brand);

        return back();
    }
}
