<?php

namespace App\Http\Controllers\Cms\PPOB;

use App\Actions\Cms\PPOB\ImportDigiflazz\SyncPPOBProductAction;
use App\Http\Controllers\Controller;
use App\Models\PPOB\PPOBProduct;
use App\Services\DigiflazzService;
use App\Traits\WithGetFilterData;
use Illuminate\Support\Facades\Gate;

class ImportDigiflazzController extends Controller
{
    use WithGetFilterData;

    protected string $resource = PPOBProduct::class;

    public function __construct(
        public readonly DigiflazzService $digiflazzService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('view'.$this->resource);

        $products = $this->digiflazzService->getPrepaidProducts();

        $groupedProducts = collect($products)->groupBy('category')->map(function ($items, $category) {
            return [
                'name' => $category,
                'brands' => $items->groupBy('brand')->map(function ($brandItems, $brand) {
                    return [
                        'name' => $brand,
                        'products' => $brandItems->values(),
                    ];
                })->values(),
            ];
        })->values();

        return inertia('cms/ppob/import-digiflazz/Index', [
            'products' => $groupedProducts,
        ]);
    }

    /**
     * Sync newly imported Digiflazz products.
     */
    public function sync(SyncPPOBProductAction $action)
    {
        Gate::authorize('create'.$this->resource);

        $action->handle();

        return back();
    }
}
