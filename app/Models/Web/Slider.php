<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Slider extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'title',
        'order',
        'link',
        'status',
    ];
}
