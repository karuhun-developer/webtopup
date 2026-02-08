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
        return inertia('main/PrivacyPolicy');
    }

    /**
     * Display the terms and conditions page.
     */
    public function terms()
    {
        return inertia('main/Terms');
    }
}
