<?php

namespace App\Http\Controllers\Cms\Management;

use App\Actions\Cms\Management\User\DeleteUserAction;
use App\Actions\Cms\Management\User\StoreUserAction;
use App\Actions\Cms\Management\User\UpdateUserAction;
use App\Actions\Cms\Management\User\UpdateUserPasswordAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Management\User\StoreUserRequest;
use App\Http\Requests\Cms\Management\User\UpdateUserPasswordRequest;
use App\Http\Requests\Cms\Management\User\UpdateUserRequest;
use App\Models\Spatie\Role;
use App\Models\User;
use App\Traits\WithGetFilterData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    use WithGetFilterData;

    protected string $resource = User::class;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('view'.$this->resource);

        $order = $request?->order ?? 'desc';
        $orderBy = $request?->orderBy ?? 'users.created_at';
        $paginate = $request?->paginate ?? 10;
        $searchBySpecific = $request?->searchBySpecific ?? '';
        $search = $request?->search ?? '';

        // Query
        $model = User::query()
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->select(
                'users.*',
                'roles.name as role_name',
            );

        $model = $this->getDataWithFilter(
            model: $model,
            searchBy: [
                'roles.name',
                'users.name',
                'users.email',
                'users.phone',
                'users.email_verified_at',
                'users.created_at',
            ],
            order: $order,
            orderBy: $orderBy,
            paginate: $paginate,
            searchBySpecific: $searchBySpecific,
            s: $search,
        );

        return inertia('cms/management/user/Index', [
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

        return inertia('cms/management/user/Create', [
            'roles' => Role::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request, StoreUserAction $action)
    {
        Gate::authorize('create'.$this->resource);

        $action->handle($request->validated());

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        Gate::authorize('update'.$this->resource);

        // Get user role name
        $user->role_name = $user->getRoleNames()->first();

        return inertia('cms/management/user/Edit', [
            'user' => $user,
            'roles' => Role::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $action)
    {
        Gate::authorize('update'.$this->resource);

        $action->handle($user, $request->validated());

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, DeleteUserAction $action)
    {
        Gate::authorize('delete'.$this->resource);

        $action->handle($user);

        return back();
    }

    /**
     * Show the form for editing password of the specified resource.
     */
    public function editPassword(User $user)
    {
        Gate::authorize('update'.$this->resource);

        return inertia('cms/management/user/EditPassword', [
            'user' => $user,
        ]);
    }

    /**
     * Update the password of the specified resource in storage.
     */
    public function updatePassword(UpdateUserPasswordRequest $request, User $user, UpdateUserPasswordAction $action)
    {
        Gate::authorize('update'.$this->resource);

        $action->handle($user, $request->validated());

        return back();
    }
}
