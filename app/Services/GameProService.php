<?php

namespace App\Services;

use App\Models\Account\Account;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GameProService
{
    public array $gameList = [
        'mobilelegend',
        'freefire',
    ];

    /**
     * Constructor for GameProService
     **/
    public function __construct(
        public ?string $apiUrl = null,
        public ?string $secretKey = null,
        public ?string $merchantId = null,
    )
    {
        $this->apiUrl = $apiUrl ?? config('gamepro.api_url');
        $this->secretKey = $secretKey ?? config('gamepro.secret_key');
        $this->merchantId = $merchantId ?? config('gamepro.merchant_id');
    }

    public function isIdValid(
        string $game,
        ?string $server,
        string $uid,
    ) {
        try {
            $cachedData = Cache::remember('gameidpro:'.$game.':'.$server.':'.$uid, now()->addMonth(), function () use ($game, $server, $uid) {
                // Check game account
                $response = Http::get($this->apiUrl.'/merchant/'.$this->merchantId.'/cek-username/'.$game, [
                    'user_id' => $uid.$server,
                    'signature' => md5($this->merchantId.$this->secretKey),
                ]);

                // To array
                $data = $response->json();

                return $data;
            });

            // Raise exception if status is false
            if ($cachedData['status'] === 0) {
                throw new \Exception('Invalid game ID');
            }

            return [
                'status' => true,
                'message' => $cachedData['message'],
                'data' => $cachedData['data'] ?? null,
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }
    }
    public function resolveAccount(
        string $game,
        string $uid,
        ?string $server = null,
    ): array
    {
        // Check local database first
        $account = Account::where('game', $game)
            ->where('uid', $uid)
            ->when($server, fn($q) => $q->where('server', $server))
            ->first();

        if ($account) {
            return [
                'status' => true,
                'source' => 'database',
                'data' => [
                    'username' => $account->username,
                    'meta' => $account->meta,
                ],
            ];
        }

        // Check against API
        $check = $this->isIdValid($game, $server, $uid);

        if ($check['status']) {
            // Save to database
            Account::create([
                'game' => $game,
                'uid' => $uid,
                'server' => $server,
                'username' => $check['data']['username'] ?? 'Unknown',
                'meta' => $check['data'] ?? null,
            ]);

            return [
                'status' => true,
                'source' => 'api',
                'data' => $check['data'],
            ];
        }

        return $check;
    }
}
