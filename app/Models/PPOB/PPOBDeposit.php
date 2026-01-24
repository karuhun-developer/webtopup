<?php

namespace App\Models\PPOB;

use Illuminate\Database\Eloquent\Model;

class PPOBDeposit extends Model
{
    protected $fillable = [
        'bank',
        'payment_method',
        'owner_name',
        'account_number',
        'amount',
        'notes',
        'status',
    ];
}
