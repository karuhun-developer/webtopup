<?php

namespace App\Http\Controllers\Cms\Setting;

use App\Actions\Cms\Setting\Setting\SaveSettingAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Setting\Setting\SaveSettingRequest;
use App\Models\Setting\Setting;
use App\Traits\WithGetFilterData;
use Illuminate\Support\Facades\Gate;

class SettingController extends Controller
{
    use WithGetFilterData;

    protected string $resource = Setting::class;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('view'.$this->resource);

        return inertia('cms/setting/setting/Index', [
            'setting' => Setting::first(),
        ]);
    }

    /**
     * Save the specified resource in storage.
     */
    public function save(SaveSettingRequest $request, SaveSettingAction $action)
    {
        Gate::authorize('update'.$this->resource);

        $action->handle($request->validated());

        return back();
    }
}
