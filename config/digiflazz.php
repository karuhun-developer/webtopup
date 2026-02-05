<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Digiflazz Username
    |--------------------------------------------------------------------------
    | Diambil dari menu 'Atur Koneksi' > 'API'.
    |
    */
    'username' => env('DIGIFLAZZ_USERNAME'),

    /*
    |--------------------------------------------------------------------------
    | Digiflazz API Key
    |--------------------------------------------------------------------------
    | Diambil dari menu 'Atur Koneksi' > 'API'.
    | Gunakan Production Key atau Development Key dari Digiflazz.
    | Rekomendasi menggunakan Production Key untuk aplikasi yang sudah siap digunakan.
    |
    */
    'api_key' => env('DIGIFLAZZ_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Digiflazz Webhook Secret Key
    |--------------------------------------------------------------------------
    |
    | Secret Key ini digunakan untuk memverifikasi keaslian webhook
    | yang dikirim oleh Digiflazz.
    | Diambil dari menu 'Atur Koneksi' > 'API' > Webhook.
    |
    */
    'webhook_secret' => env('DIGIFLAZZ_WEBHOOK_SECRET'),

];
