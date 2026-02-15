<?php

namespace App\Models\PPOB;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class PPOBProduct extends Model implements HasMedia
{
    use HasSlug, InteractsWithMedia;

    protected $fillable = [
        'p_p_o_b_brand_id',
        'name',
        'slug',
        'sku',
        'description',
        'delay',
        'buy_price',
        'sell_price',
        'status',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom([
                'name',
            ])
            ->saveSlugsTo('slug')
            ->usingSuffixGenerator(
                fn (string $slug, int $iteration) => bin2hex(random_bytes(4))
            );
    }

    public function brand()
    {
        return $this->belongsTo(PPOBBrand::class, 'p_p_o_b_brand_id');
    }
}
