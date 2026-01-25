<?php

namespace App\Http\Controllers\Cms\PPOB;

use App\Actions\Cms\PPOB\PPOBCategory\DeletePPOBCategoryAction;
use App\Actions\Cms\PPOB\PPOBCategory\StorePPOBCategoryAction;
use App\Actions\Cms\PPOB\PPOBCategory\UpdatePPOBCategoryAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\PPOB\PPOBCategory\StorePPOBCategoryRequest;
use App\Http\Requests\Cms\PPOB\PPOBCategory\UpdatePPOBCategoryRequest;
use App\Models\PPOB\PPOBCategory;
use App\Traits\WithGetFilterData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PPOBCategoryController extends Controller
{
    use WithGetFilterData;

    protected string $resource = PPOBCategory::class;

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
            model: PPOBCategory::with('media'),
            searchBy: [
                'name',
                'description',
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

        return inertia('cms/ppob/ppob-category/Index', [
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

        return inertia('cms/ppob/ppob-category/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePPOBCategoryRequest $request, StorePPOBCategoryAction $action)
    {
        Gate::authorize('create'.$this->resource);

        $action->handle($request->validated());

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(PPOBCategory $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PPOBCategory $category)
    {
        Gate::authorize('update'.$this->resource);

        $category->image = $category->getFirstMediaUrl('image');

        return inertia('cms/ppob/ppob-category/Edit', [
            'category' => $category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePPOBCategoryRequest $request, PPOBCategory $category, UpdatePPOBCategoryAction $action)
    {
        Gate::authorize('update'.$this->resource);

        $action->handle($category, $request->validated());

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PPOBCategory $category, DeletePPOBCategoryAction $action)
    {
        Gate::authorize('delete'.$this->resource);

        $action->handle($category);

        return back();
    }
}
