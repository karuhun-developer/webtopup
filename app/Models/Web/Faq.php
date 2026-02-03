<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = [
        'question',
        'answer',
        'order',
        'status',
    ];
}
