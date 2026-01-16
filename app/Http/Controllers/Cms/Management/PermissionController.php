<?php

namespace App\Http\Controllers\Cms\Management;

use App\Actions\Cms\Management\Permission\DeletePermissionAction;
use App\Actions\Cms\Management\Permission\StorePermissionAction;
use App\Actions\Cms\Management\Permission\UpdatePermissionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Management\Permission\StorePermissionRequest;
use App\Http\Requests\Cms\Management\Permission\UpdatePermissionRequest;
use App\Models\Spatie\Permission;
use App\Traits\WithGetFilterData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PermissionController extends Controller
{
    use WithGetFilterData;

    protected string $resource = Permission::class;

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
            model: new Permission,
            searchBy: [
                'name',
                'guard_name',
            ],
            order: $order,
            orderBy: $orderBy,
            paginate: $paginate,
            searchBySpecific: $searchBySpecific,
            s: $search,
        );

        return inertia('cms/management/permission/Index', [
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

        return inertia('cms/management/permission/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePermissionRequest $request, StorePermissionAction $action)
    {
        Gate::authorize('create'.$this->resource);

        $action->handle($request->validated());

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        Gate::authorize('update'.$this->resource);

        return inertia('cms/management/permission/Edit', [
            'permission' => $permission,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermissionRequest $request, Permission $permission, UpdatePermissionAction $action)
    {
        Gate::authorize('update'.$this->resource);

        $action->handle($permission, $request->validated());

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission, DeletePermissionAction $action)
    {
        Gate::authorize('delete'.$this->resource);

        $action->handle($permission);

        return back();
    }
}
