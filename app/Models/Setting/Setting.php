<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];
}
