<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Payment extends Model implements HasMedia
{
    use InteractsWithMedia, LogsActivity;

    protected $fillable = [
        'driver',
        'payable_type',
        'payable_id',
        'order_id',
        'transaction_id',
        'payment_type',
        'account_number',
        'account_code',
        'channel',
        'expired_at',
        'paid_at',
        'amount',
        'gateway_token',
        'gateway_payload',
        'payment_url',
        'unique_amount',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'paid_at' => 'datetime',
        'gateway_token' => 'encrypted',
        'gateway_payload' => 'encrypted:array',
    ];

    protected $hidden = ['gateway_token', 'gateway_payload'];

    // Get the activity log options.
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])->logExcept(['gateway_token', 'gateway_payload']);
    }

    public function payable()
    {
        return $this->morphTo();
    }
}
