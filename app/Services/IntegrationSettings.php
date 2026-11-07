<?php

namespace App\Services;

use App\Models\Setting\Setting;
use Illuminate\Support\Facades\Cache;

class IntegrationSettings
{
    public const CREDENTIALS = [
        'h2h_api_key' => 'integrations.h2h_api_key',
        'h2h_webhook_secret' => 'integrations.h2h_webhook_secret',
        'digiflazz_username' => 'digiflazz.username',
        'digiflazz_api_key' => 'digiflazz.api_key',
        'digiflazz_webhook_secret' => 'digiflazz.webhook_secret',
        'apigames_merchant_id' => 'gamepro.merchant_id',
        'apigames_secret_key' => 'gamepro.secret_key',
        'midtrans_server_key' => 'midtrans.server_key',
        'midtrans_client_key' => 'midtrans.client_key',
        'qris_api_key' => 'integrations.qris_api_key',
        'paywuz_api_key' => 'integrations.paywuz_api_key',
    ];

    public function value(string $key, mixed $default = null): mixed
    {
        return getSetting($key) ?? config('integrations.'.$key, $default);
    }

    public function credential(string $key): string
    {
        $credentials = Cache::remember('integration:credentials', 60, fn () => Setting::first()?->credentials ?? []);

        return (string) ($credentials[$key] ?? config(self::CREDENTIALS[$key] ?? '', ''));
    }

    public function configuredCredentials(): array
    {
        $configured = [];
        foreach (array_keys(self::CREDENTIALS) as $key) {
            $configured[$key] = $this->credential($key) !== '';
        }

        return $configured;
    }

    public function publicSettings(): array
    {
        return array_diff_key(getSetting(), array_flip([...array_keys(self::CREDENTIALS), 'credentials']));
    }
}
