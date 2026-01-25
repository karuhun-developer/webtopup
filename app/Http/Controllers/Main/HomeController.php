<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('main/Home', [
            'products' => [
                [
                    'id' => 1,
                    'title' => 'AOV',
                    'image' => 'https://placehold.co/300x400/4F46E5/FFFFFF?text=AOV',
                    'rating' => 5,
                ],
                [
                    'id' => 2,
                    'title' => 'DOTA 2',
                    'image' => 'https://placehold.co/300x400/DC2626/FFFFFF?text=DOTA+2',
                    'rating' => 5,
                ],
                [
                    'id' => 3,
                    'title' => 'Honor of Kings',
                    'image' => 'https://placehold.co/300x400/F59E0B/FFFFFF?text=Honor+of+Kings',
                    'rating' => 5,
                ],
                [
                    'id' => 4,
                    'title' => 'Mobile Legends',
                    'image' => 'https://placehold.co/300x400/7C3AED/FFFFFF?text=Mobile+Legends',
                    'rating' => 5,
                ],
                [
                    'id' => 5,
                    'title' => 'Unite',
                    'image' => 'https://placehold.co/300x400/10B981/FFFFFF?text=Unite',
                    'rating' => 5,
                ],
                [
                    'id' => 6,
                    'title' => 'Heroes of the Storm',
                    'image' => 'https://placehold.co/300x400/3B82F6/FFFFFF?text=Heroes+of+the+Storm',
                    'rating' => 5,
                ],
                [
                    'id' => 7,
                    'title' => 'Vainglory',
                    'image' => 'https://placehold.co/300x400/EC4899/FFFFFF?text=Vainglory',
                    'rating' => 5,
                ],
                [
                    'id' => 8,
                    'title' => 'Arena of Valor',
                    'image' => 'https://placehold.co/300x400/8B5CF6/FFFFFF?text=Arena+of+Valor',
                    'rating' => 5,
                ],
            ],
        ]);
    }
}
