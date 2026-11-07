<?php

namespace App\Http\Controllers\Cms\Setting;

use App\Actions\Cms\Setting\Setting\SaveSettingAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Setting\Setting\SaveSettingRequest;
use App\Models\Setting\Setting;
use App\Services\IntegrationSettings;
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

        $integrations = app(IntegrationSettings::class);
        $setting = Setting::firstOrNew();
        $defaults = [];
        foreach (['supplier_provider', 'username_provider', 'payment_gateway', 'h2h_base_url', 'qris_base_url', 'paywuz_base_url', 'qris_provider'] as $key) {
            $defaults[$key] = $integrations->value($key);
        }
        $defaults['midtrans_is_production'] = config('midtrans.is_production', false);
        $defaults['apigames_base_url'] = config('gamepro.api_url');
        $setting->value = [...$defaults, ...($setting->value ?? [])];
        $callbackBaseUrl = rtrim(config('app.url'), '/');

        return inertia('cms/setting/setting/Index', [
            'setting' => $setting,
            'configuredCredentials' => app(IntegrationSettings::class)->configuredCredentials(),
            'callbackUrls' => [
                'h2h' => $callbackBaseUrl.route('api.v1.h2h.callback', [], false),
                'digiflazz' => $callbackBaseUrl.route('api.v1.digiflazz.callback', [], false),
                'midtrans' => $callbackBaseUrl.route('api.v1.midtrans.callback', [], false),
                'paywuz' => $callbackBaseUrl.route('api.v1.paywuz.callback', [], false),
            ],
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
