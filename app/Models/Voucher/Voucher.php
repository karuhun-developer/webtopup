<?php

namespace App\Models\Voucher;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'type', // FIXED_AMOUNT, PERCENTAGE
        'fixed_amount',
        'percentage',
        'start_date',
        'end_date',
        'min_purchase_amount',
        'usage_limit',
        'used_count',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'status' => 'boolean',
        'fixed_amount' => 'integer',
        'percentage' => 'decimal:2',
        'min_purchase_amount' => 'integer',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
    ];
}
