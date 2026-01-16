<?php

namespace App\Http\Controllers\Cms\Management;

use App\Actions\Cms\Management\Role\DeleteRoleAction;
use App\Actions\Cms\Management\Role\StoreRoleAction;
use App\Actions\Cms\Management\Role\UpdateRoleAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Management\Role\StoreRoleRequest;
use App\Http\Requests\Cms\Management\Role\UpdateRoleRequest;
use App\Models\Spatie\Role;
use App\Traits\WithGetFilterData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
    use WithGetFilterData;

    protected string $resource = Role::class;

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
            model: new Role,
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

        return inertia('cms/management/role/Index', [
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

        return inertia('cms/management/role/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request, StoreRoleAction $action)
    {
        Gate::authorize('create'.$this->resource);

        $action->handle($request->validated());

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        Gate::authorize('update'.$this->resource);

        return inertia('cms/management/role/Edit', [
            'role' => $role,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role, UpdateRoleAction $action)
    {
        Gate::authorize('update'.$this->resource);

        $action->handle($role, $request->validated());

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role, DeleteRoleAction $action)
    {
        Gate::authorize('delete'.$this->resource);

        $action->handle($role);

        return back();
    }
}
