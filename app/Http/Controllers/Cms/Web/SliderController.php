<?php

namespace App\Http\Controllers\Cms\Web;

use App\Actions\Cms\Web\Slider\DeleteSliderAction;
use App\Actions\Cms\Web\Slider\StoreSliderAction;
use App\Actions\Cms\Web\Slider\UpdateSliderAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Web\Slider\StoreSliderRequest;
use App\Http\Requests\Cms\Web\Slider\UpdateSliderRequest;
use App\Models\Web\Slider;
use App\Traits\WithGetFilterData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SliderController extends Controller
{
    use WithGetFilterData;

    protected string $resource = Slider::class;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('view'.$this->resource);

        $order = $request?->order ?? 'desc';
        $orderBy = $request?->orderBy ?? 'order';
        $paginate = $request?->paginate ?? 10;
        $searchBySpecific = $request?->searchBySpecific ?? '';
        $search = $request?->search ?? '';

        $model = $this->getDataWithFilter(
            model: Slider::with('media'),
            searchBy: [
                'title',
                'order',
                'link',
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

        return inertia('cms/web/slider/Index', [
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

        return inertia('cms/web/slider/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSliderRequest $request, StoreSliderAction $action)
    {
        Gate::authorize('create'.$this->resource);

        $action->handle($request->validated());

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Slider $slider)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Slider $slider)
    {
        Gate::authorize('update'.$this->resource);

        $slider->image = $slider->getFirstMediaUrl('image');

        return inertia('cms/web/slider/Edit', [
            'slider' => $slider,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSliderRequest $request, Slider $slider, UpdateSliderAction $action)
    {
        Gate::authorize('update'.$this->resource);

        $action->handle($slider, $request->validated());

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Slider $slider, DeleteSliderAction $action)
    {
        Gate::authorize('delete'.$this->resource);

        $action->handle($slider);

        return back();
    }
}
