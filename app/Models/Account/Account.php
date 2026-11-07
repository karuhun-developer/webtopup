<?php

namespace App\Models\Account;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'game',
        'uid',
        'server',
        'username',
        'meta',
        'provider',
        'identity_key',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (Account $account) {
            $account->provider ??= 'apigames';
            $account->game = trim($account->game);
            $account->uid = trim($account->uid);
            $account->server = trim($account->server ?? '');
            $account->identity_key = self::identity($account->provider, $account->game, $account->uid, $account->server);
        });
    }

    public static function identity(string $provider, string $game, string $uid, ?string $server): string
    {
        return hash('sha256', json_encode([$provider, trim($game), trim($uid), trim($server ?? '')]));
    }
}
