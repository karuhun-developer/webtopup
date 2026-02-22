<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;

class ContentController extends Controller
{
    /**
     * Display the privacy policy page.
     */
    public function privacyPolicy()
    {
        $settingTitle = getSetting('title');
        $settingFavicon = getSetting('favicon') ?: '/favicon.svg';

        return inertia()->render('main/PrivacyPolicy')->withViewData([
            'meta' => [
                'title' => 'Kebijakan Privasi - ' . $settingTitle,
                'description' => 'Kebijakan Privasi ' . $settingTitle . '. Pelajari bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda saat menggunakan layanan kami.',
                'keywords' => 'kebijakan privasi, privacy policy, ' . $settingTitle . ', perlindungan data, keamanan informasi',
                'author' => $settingTitle,
                'application_name' => $settingTitle,
                'url' => route('privacy-policy'),
                'image' => config('app.url') . $settingFavicon,
            ]
        ]);
    }

    /**
     * Display the terms and conditions page.
     */
    public function terms()
    {
        $settingTitle = getSetting('title');
        $settingFavicon = getSetting('favicon') ?: '/favicon.svg';

        return inertia()->render('main/Terms')->withViewData([
            'meta' => [
                'title' => 'Syarat & Ketentuan - ' . $settingTitle,
                'description' => 'Syarat & Ketentuan ' . $settingTitle . '. Baca ketentuan penggunaan layanan kami untuk pengalaman transaksi yang aman dan nyaman.',
                'keywords' => 'syarat dan ketentuan, terms and conditions, ' . $settingTitle . ', aturan penggunaan, perjanjian pengguna',
                'author' => $settingTitle,
                'application_name' => $settingTitle,
                'url' => route('terms'),
                'image' => config('app.url') . $settingFavicon,
            ]
        ]);
    }
}
