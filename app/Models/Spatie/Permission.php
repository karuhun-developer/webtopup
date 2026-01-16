<?php

namespace App\Models\Spatie;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'guard_name',
    ];
}
