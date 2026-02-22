<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\PPOB\PPOBBrand;
use App\Models\Web\Faq;
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

        $settingTitle = getSetting('title');
        $settingFavicon = getSetting('favicon') ?: '/favicon.svg';

        return inertia()->render('main/BrandDetail', [
            'brand' => $brand,
            'faqs' => Faq::where('status', true)->orderBy('order', 'asc')->get(),
        ])->withViewData([
            'meta' => [
                'title' => "{$brand->name} - Top Up Murah & Cepat | {$settingTitle}",
                'description' => "Top up {$brand->name} termurah dan terpercaya di {$settingTitle}. Proses instan, tersedia berbagai metode pembayaran.",
                'keywords' => "top up {$brand->name}, beli {$brand->name}, harga {$brand->name}, {$brand->name} murah, {$settingTitle}, topup game",
                'author' => $settingTitle,
                'application_name' => $settingTitle,
                'url' => route('product.show', $brand->slug),
                'image' => $brand->image ?: (config('app.url') . $settingFavicon),
            ]
        ]);
    }
}
