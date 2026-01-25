<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Triyatna\Digiflazz\Digiflazz;

class DigiflazzService
{
    // Cache key prefix
    protected string $prefixCacheKey = 'digiflazz:';

    /**
     * Get the current saldo from Digiflazz API.
     *
     * @return int
     */
    public function getSaldo(bool $refresh = false)
    {
        try {
            // Force to refresh balance
            if ($refresh) {
                Cache::forget($this->prefixCacheKey.'balance');
            }

            // 60 to 120 seconds cache
            $balance = Cache::flexible(key: $this->prefixCacheKey.'balance', ttl: [
                60,
                120,
            ], callback: function () {
                $response = Digiflazz::checkBalance();
                // Cattch Error
                if (! $response->isSuccess()) {
                    throw new \Exception('Failed to fetch balance from Digiflazz');
                }

                return $response->get('deposit');
            });

            return (int) $balance;
        } catch (\Exception $e) {
            Log::error('DigiflazzService@getSaldo: '.$e->getMessage());

            return 0;
        }
    }

    /**
     * Get prepaid products from Digiflazz API.
     *
     * @param  string|null  $brand  e.g. 'TELKOMSEL', 'INDOSAT', 'MOBILE LEGENDS', etc.
     * @param  string|null  $category  e.g. 'PULSA', 'DATA', 'GAME', etc.
     * @return array
     */
    public function getPrepaidProducts(?string $brand = null, ?string $category = null, bool $refresh = false)
    {
        // Time to live for cache
        $ttl = now()->addDay();

        if ($refresh) {
            // Invalidate cache
            $cacheKey = $this->prefixCacheKey.'prepaid_products';
            if ($brand) {
                $cacheKey .= ":brand:$brand";
            }
            if ($category) {
                $cacheKey .= ":category:$category";
            }
            Cache::forget($cacheKey);
        }

        // Cache key
        $cacheKey = $this->prefixCacheKey.'prepaid_products';
        if ($brand) {
            $cacheKey .= ":brand:$brand";
        }
        if ($category) {
            $cacheKey .= ":category:$category";
        }

        try {
            $product = Cache::remember($cacheKey, $ttl, function () use ($brand, $category) {
                // Get Price List
                $response = Digiflazz::getPriceList('prepaid', [
                    'brand' => $brand,
                    'category' => $category,
                ]);

                // Catch Error
                if (! $response->isSuccess()) {
                    throw new \Exception('Failed to fetch products from Digiflazz with brand '.$brand.' and category '.$category);
                }

                return $response->data();
            });

            return $product;
        } catch (\Exception $e) {
            Log::error('DigiflazzService@getPrepaidProducts: '.$e->getMessage());

            return [];
        }
    }
}
