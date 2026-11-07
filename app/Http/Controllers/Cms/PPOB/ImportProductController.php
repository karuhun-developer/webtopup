<?php

namespace App\Http\Controllers\Cms\PPOB;

use App\Actions\Cms\PPOB\ImportProduct\SyncPPOBProductAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\PPOB\ImportProductRequest;
use App\Models\PPOB\PPOBProduct;
use App\Services\IntegrationSettings;
use App\Services\ProductProviderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Response;
use Throwable;

class ImportProductController extends Controller
{
    protected string $resource = PPOBProduct::class;

    public function index(Request $request, ProductProviderService $providers, IntegrationSettings $settings): Response
    {
        Gate::authorize('view'.$this->resource);
        $provider = $request->query('provider', $settings->value('supplier_provider'));
        abort_unless(in_array($provider, ['h2h', 'digiflazz'], true), 422);
        $error = null;
        try {
            $products = $providers->products($provider);
        } catch (Throwable $e) {
            report($e);
            $products = [];
            $error = 'Katalog belum tersedia. Periksa credential provider di Settings.';
        }

        return inertia('cms/ppob/import-product/Index', [
            'provider' => $provider, 'error' => $error,
            'products' => collect($products)->groupBy('category')->map(fn ($items, $category) => [
                'name' => $category,
                'brands' => $items->groupBy('brand')->map(fn ($items, $brand) => [
                    'name' => $brand, 'products' => $items->values(),
                ])->values(),
            ])->values(),
        ]);
    }

    public function sync(ImportProductRequest $request, SyncPPOBProductAction $action): RedirectResponse
    {
        Gate::authorize('create'.$this->resource);
        try {
            $action->handle($request->validated('provider'));
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors(['provider' => 'Import gagal. Periksa credential dan ketersediaan provider.']);
        }

        return back();
    }
}
