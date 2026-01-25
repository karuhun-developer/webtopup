<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function show(string $product): Response
    {
        return Inertia::render('main/ProductDetail', [
            'productName' => ucwords(str_replace('-', ' ', $product)),
            'productImage' => 'https://placehold.co/400x200/7C3AED/FFFFFF?text='.urlencode(ucwords(str_replace('-', ' ', $product))),
        ]);
    }
}
