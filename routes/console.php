<?php

use App\Jobs\CheckGatewayPayments;
use App\Jobs\CheckProviderOrders;
use App\Jobs\ExpirePayments;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new ExpirePayments)->hourly();
Schedule::job(new CheckGatewayPayments)->everyMinute()->withoutOverlapping();
Schedule::job(new CheckProviderOrders)->everyMinute()->withoutOverlapping();
