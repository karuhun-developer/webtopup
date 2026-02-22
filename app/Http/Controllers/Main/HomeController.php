<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\PPOB\PPOBBrand;
use App\Models\PPOB\PPOBCategory;
use App\Models\Web\Slider;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the main home page.
     */
    public function index(Request $request)
    {
        // Filter category
        $category = null;
        if ($request->has('category')) {
            $category = PPOBCategory::where('slug', $request->query('category'))->first();
        }

        $settingTitle = getSetting('title');
        $settingFavicon = getSetting('favicon') ?: '/favicon.svg';

        return inertia()->render('main/Home', [
            'sliders' => Slider::query()
                ->with('media')
                ->where('status', true)
                ->orderBy('order')
                ->get()
                ->map(function ($slider) {
                    $slider->image = $slider->getFirstMediaUrl('image');
                    $slider->makeHidden('media');

                    return $slider;
                }),
            'brands' => inertia()->scroll(fn () => PPOBBrand::query()
                ->with('category', 'media')
                ->when($category, fn ($query) => $query->where('p_p_o_b_category_id', $category->id))
                ->where('status', true)
                ->orderBy('order')
                ->simplePaginate(12)
                ->through(function ($brand) {
                    $brand->image = $brand->getFirstMediaUrl('image');
                    $brand->makeHidden('media');

                    return $brand;
                })),
            'featured_brands' => PPOBBrand::query()
                ->with('category', 'media')
                ->where('featured', true)
                ->where('status', true)
                ->orderBy('order')
                ->limit(6)
                ->get()
                ->map(function ($brand) {
                    $brand->image = $brand->getFirstMediaUrl('image');
                    $brand->makeHidden('media');

                    return $brand;
                }),
            'categories' => PPOBCategory::query()
                ->withCount('activeBrands', 'media')
                ->where('status', true)
                ->get()
                ->map(function ($category) {
                    $category->image = $category->getFirstMediaUrl('image');
                    $category->makeHidden('media');

                    return $category;
                }),
        ])->withViewData([
            'meta' => [
                'title' => $settingTitle,
                'description' => "Selamat datang di {$settingTitle}, tempat topup game termurah dan terpercaya! Nikmati berbagai penawaran menarik untuk topup game favoritmu dengan harga terbaik. Bergabunglah sekarang dan rasakan pengalaman topup yang mudah, cepat, dan aman hanya di {$settingTitle}!",
                'keywords' => "{$settingTitle}, topup game, topup murah, topup terpercaya, penawaran menarik, harga terbaik, pengalaman topup mudah, cepat, aman",
                'author' => $settingTitle,
                'application_name' => $settingTitle,
                'url' => route('home'),
                'image' => config('app.url') . $settingFavicon,
            ]
        ]);
    }
}
