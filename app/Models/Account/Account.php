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
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}
