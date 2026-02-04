<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\PPOB\PPOBBrand;
use Inertia\Inertia;
use Inertia\Response;

class BrandController extends Controller
{
    public function show(PPOBBrand $brand): Response
    {
        $brand->load(['products.media', 'category']);

        $brand->image = $brand->getFirstMediaUrl('image');
        $brand->banner = $brand->getFirstMediaUrl('banner');
        $brand->default_product_image = $brand->getFirstMediaUrl('default_product_image');

        $brand->products->each(function ($product) use ($brand) {
            $product->image = $product->getFirstMediaUrl('image') ?: $brand->default_product_image;
            $product->makeHidden('media');
        });

        $brand->makeHidden('media');

        return inertia('main/BrandDetail', [
            'brand' => $brand,
        ]);
    }
}
