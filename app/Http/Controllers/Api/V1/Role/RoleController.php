<?php

namespace App\Http\Controllers\Api\V1\Role;

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

        return $this->responseWithSuccess($model);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request, StoreRoleAction $action)
    {
        Gate::authorize('create'.$this->resource);

        return $this->responseWithCreated($action->handle($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        Gate::authorize('show'.$this->resource);

        return $this->responseWithSuccess($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role, UpdateRoleAction $action)
    {
        Gate::authorize('update'.$this->resource);

        return $this->responseWithSuccess($action->handle($role, $request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role, DeleteRoleAction $action)
    {
        Gate::authorize('delete'.$this->resource);

        return $this->responseWithSuccess($action->handle($role));
    }
}
