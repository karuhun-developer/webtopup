<?php

namespace App\Http\Controllers\Cms\PPOB;

use App\Actions\Cms\PPOB\ImportProduct\SyncPPOBProductAction;
use App\Exceptions\ProductCatalogPendingException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\PPOB\ImportProductRequest;
use App\Models\PPOB\PPOBProduct;
use App\Services\IntegrationSettings;
use App\Services\ProductProviderService;
use Illuminate\Http\Client\RequestException;
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
        $catalog = ['products' => [], 'complete' => false, 'count' => 0, 'retry_after' => 0];
        try {
            $catalog = $providers->catalog($provider);
        } catch (Throwable $e) {
            report($e);
            $error = $e instanceof RequestException ? match ($e->response->status()) {
                401 => 'API key provider tidak valid. Periksa credential di Settings.',
                403 => 'API key belum memiliki akses katalog. Periksa izin key di dashboard provider.',
                429 => 'Provider sedang membatasi request. Coba kembali sebentar lagi.',
                default => 'Provider belum dapat mengirim katalog. Coba kembali sebentar lagi.',
            } : 'Katalog belum tersedia. Periksa credential provider di Settings.';
        }

        return inertia('cms/ppob/import-product/Index', [
            'provider' => $provider, 'error' => $error,
            'catalog' => array_diff_key($catalog, ['products' => true]),
            'products' => collect($catalog['products'])->groupBy('category')->map(fn ($items, $category) => [
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
        } catch (ProductCatalogPendingException $e) {
            return back()->withErrors(['provider' => $e->getMessage()]);
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors(['provider' => 'Import gagal. Periksa credential dan ketersediaan provider.']);
        }

        return back();
    }
}
