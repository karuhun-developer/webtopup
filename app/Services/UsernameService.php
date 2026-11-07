<?php

namespace App\Services;

use App\Models\Account\Account;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Throwable;

class UsernameService
{
    public function __construct(
        public readonly IntegrationSettings $settings,
        public readonly H2hService $h2h,
        public readonly GameProService $apigames,
    ) {}

    public static function gameForBrand(string $brand): string
    {
        $name = Str::lower($brand);

        return match (true) {
            Str::contains($name, ['mobile legend', 'mobilelegend']) => 'mobilelegend',
            Str::contains($name, ['free fire', 'freefire']) => 'freefire',
            default => Str::slug($name, ''),
        };
    }

    public function resolveAccount(string $game, string $uid, ?string $server = null, ?string $provider = null, ?string $serviceCode = null): array
    {
        $provider ??= $this->settings->value('username_provider');
        $uid = trim($uid);
        $server = trim($server ?? '');
        $identity = Account::identity($provider, $game, $uid, $server);

        try {
            return Cache::lock('username:'.$identity, 60)->block(5, function () use ($identity, $provider, $game, $uid, $server, $serviceCode) {
                $account = Account::where('identity_key', $identity)->where('updated_at', '>=', now()->subMonth())->first();
                if ($account) {
                    return ['status' => true, 'source' => 'database', 'data' => ['username' => $account->username]];
                }

                $result = match ($provider) {
                    'h2h' => $this->h2h->checkUsername($game, $uid, $server, $serviceCode),
                    'apigames' => $this->apigames->isIdValid($game, $server, $uid),
                    default => ['status' => false, 'message' => 'Provider username tidak valid.'],
                };
                if (($result['status'] ?? false) && ! empty($result['data']['username'])) {
                    Account::updateOrCreate(['identity_key' => $identity], [
                        'provider' => $provider, 'game' => $game, 'uid' => $uid, 'server' => $server,
                        'username' => $result['data']['username'], 'meta' => $result['data'],
                    ]);
                }

                return $result;
            });
        } catch (Throwable $e) {
            report($e);

            return ['status' => false, 'message' => 'Layanan cek username belum tersedia. Silakan coba lagi.'];
        }
    }
}
