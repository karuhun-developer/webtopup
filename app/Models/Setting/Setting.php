<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'value',
        'credentials',
    ];

    protected $casts = [
        'value' => 'array',
        'credentials' => 'encrypted:array',
    ];

    protected $hidden = ['credentials'];
}
