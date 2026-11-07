<?php

return [
    'supplier_provider' => env('SUPPLIER_PROVIDER', 'h2h'),
    'username_provider' => env('USERNAME_PROVIDER', 'h2h'),
    'payment_gateway' => env('PAYMENT_GATEWAY', 'karuhun_qris'),
    'h2h_base_url' => env('H2H_BASE_URL', 'https://h2h.karuhundeveloper.com'),
    'h2h_api_key' => env('H2H_API_KEY'),
    'h2h_webhook_secret' => env('H2H_WEBHOOK_SECRET'),
    'qris_base_url' => env('KARUHUN_QRIS_BASE_URL', 'https://qris.karuhundeveloper.com'),
    'qris_api_key' => env('KARUHUN_QRIS_API_KEY'),
    'qris_provider' => env('KARUHUN_QRIS_PROVIDER'),
    'paywuz_base_url' => env('PAYWUZ_BASE_URL', 'https://api.paywuz.id'),
    'paywuz_api_key' => env('PAYWUZ_API_KEY'),
];
