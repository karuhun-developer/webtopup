<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GameProService
{
    public array $gameList = ['mobilelegend', 'freefire'];

    public function __construct(
        public ?string $apiUrl = null,
        public ?string $secretKey = null,
        public ?string $merchantId = null,
    ) {}

    public function isIdValid(string $game, ?string $server, string $uid): array
    {
        if (! in_array($game, $this->gameList, true)) {
            return ['status' => false, 'message' => 'Game belum didukung API Games.'];
        }
        $settings = app(IntegrationSettings::class);
        $merchant = $this->merchantId ?? $settings->credential('apigames_merchant_id');
        $secret = $this->secretKey ?? $settings->credential('apigames_secret_key');
        if ($merchant === '' || $secret === '') {
            return ['status' => false, 'message' => 'Credential API Games belum diatur.'];
        }
        $url = $this->apiUrl ?? getSetting('apigames_base_url') ?? config('gamepro.api_url');
        $data = Http::connectTimeout(5)->timeout(15)
            ->get(rtrim($url, '/').'/merchant/'.$merchant.'/cek-username/'.$game, [
                'user_id' => $uid.$server, 'signature' => md5($merchant.$secret),
            ])->throw()->json();

        return [
            'status' => (int) ($data['status'] ?? 0) === 1 && ! empty($data['data']['username']),
            'message' => 'Game ID tidak ditemukan.',
            'data' => ['username' => $data['data']['username'] ?? null],
        ];
    }

    public function resolveAccount(string $game, string $uid, ?string $server = null): array
    {
        return app(UsernameService::class)->resolveAccount($game, $uid, $server, 'apigames');
    }
}
