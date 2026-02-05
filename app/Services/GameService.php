<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Triyatna\PhpValidGame\ValidGameClient;

class GameService
{
    public array $gameList = [
        '8ballpool',
        'aethergazer',
        'aov',
        'autochess',
        'azurlane',
        'badlanders',
        'barbarq',
        'basketrio',
        'cod',
        'dragoncity',
        'freefire',
        'hago',
        'mobilelegends',
        'pb',
        'valorant',
    ];

    public readonly ValidGameClient $gameClient;

    /**
     * ## Azurlane server
     * avrora
     * lexington
     * sandy
     * washington
     * amagi
     * littleenterprise
     *
     **/
    public function __construct()
    {
        $this->gameClient = new ValidGameClient(
            proxy: config('game.proxy'),
        );
    }

    public function isIdValid(
        string $game,
        ?string $server,
        string $uid,
    ) {
        try {
            $cachedData = Cache::remember('gameid:'.$game.':'.$server.':'.$uid, now()->addMonth(), function () use ($game, $server, $uid) {
                // Check game account
                $response = $this->gameClient->check(
                    game: $game,
                    uid: $uid,
                    server: $server,
                );

                // To array
                $data = $response->toArray();

                return $data;
            });

            // Raise exception if status is false
            if (! $cachedData['status']) {
                throw new \Exception('Invalid game ID');
            }

            return $cachedData;
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
