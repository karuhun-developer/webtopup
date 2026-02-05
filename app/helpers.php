<?php

use App\Models\Setting\Setting;
use Illuminate\Support\Facades\Cache;

function numberToCurrency($value)
{
    return number_format($value, 0, ',', '.');
}

function currencyToNumber($value)
{
    return (int) str_replace('.', '', $value);
}

function getSetting(string $key)
{
    return Cache::flexible('global:settings', [
        60,
        120,
    ], function () {
        return Setting::first()->value;
    })[$key] ?? null;
}
