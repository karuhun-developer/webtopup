<?php

namespace App\Http\Controllers\Cms\Web;

use App\Actions\Cms\Web\Faq\DeleteFaqAction;
use App\Actions\Cms\Web\Faq\StoreFaqAction;
use App\Actions\Cms\Web\Faq\UpdateFaqAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Web\Faq\StoreFaqRequest;
use App\Http\Requests\Cms\Web\Faq\UpdateFaqRequest;
use App\Models\Web\Faq;
use App\Traits\WithGetFilterData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FaqController extends Controller
{
    use WithGetFilterData;

    protected string $resource = Faq::class;

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
            model: new Faq,
            searchBy: [
                'question',
                'answer',
                'order',
            ],
            order: $order,
            orderBy: $orderBy,
            paginate: $paginate,
            searchBySpecific: $searchBySpecific,
            s: $search,
        );

        return inertia('cms/web/faq/Index', [
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

        return inertia('cms/web/faq/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFaqRequest $request, StoreFaqAction $action)
    {
        Gate::authorize('create'.$this->resource);

        $action->handle($request->validated());

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Faq $faq)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Faq $faq)
    {
        Gate::authorize('update'.$this->resource);

        return inertia('cms/web/faq/Edit', [
            'faq' => $faq,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFaqRequest $request, Faq $faq, UpdateFaqAction $action)
    {
        Gate::authorize('update'.$this->resource);

        $action->handle($faq, $request->validated());

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faq $faq, DeleteFaqAction $action)
    {
        Gate::authorize('delete'.$this->resource);

        $action->handle($faq);

        return back();
    }
}
