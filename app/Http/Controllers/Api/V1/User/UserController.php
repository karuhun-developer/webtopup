<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Actions\Cms\Management\User\DeleteUserAction;
use App\Actions\Cms\Management\User\StoreUserAction;
use App\Actions\Cms\Management\User\UpdateUserAction;
use App\Actions\Cms\Management\User\UpdateUserPasswordAction;
use App\Actions\Cms\Management\User\ValidateUserEmailAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Management\User\StoreUserRequest;
use App\Http\Requests\Cms\Management\User\UpdateUserPasswordRequest;
use App\Http\Requests\Cms\Management\User\UpdateUserRequest;
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

        return $this->responseWithSuccess($model);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request, StoreUserAction $action)
    {
        Gate::authorize('create'.$this->resource);

        return $this->responseWithCreated($action->handle($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        Gate::authorize('show'.$this->resource);

        return $this->responseWithSuccess($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $action)
    {
        Gate::authorize('update'.$this->resource);

        return $this->responseWithSuccess($action->handle($user, $request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, DeleteUserAction $action)
    {
        Gate::authorize('delete'.$this->resource);

        return $this->responseWithSuccess($action->handle($user));
    }


    /**
     * Update the password of the specified resource in storage.
     */
    public function updatePassword(UpdateUserPasswordRequest $request, User $user, UpdateUserPasswordAction $action)
    {
        Gate::authorize('update'.$this->resource);

        return $this->responseWithSuccess($action->handle($user, $request->validated()));
    }

    /**
     * Validate the email of the specified resource in storage.
     */
    public function validateEmail(User $user, ValidateUserEmailAction $action)
    {
        Gate::authorize('update'.$this->resource);

        return $this->responseWithSuccess($action->handle($user));
    }
}
