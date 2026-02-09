<?php

namespace App\Models\PPOB;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class PPOBBrand extends Model implements HasMedia
{
    use HasSlug, InteractsWithMedia;

    protected $fillable = [
        'p_p_o_b_category_id',
        'name',
        'provider',
        'slug',
        'description',
        'featured',
        'order',
        'settings',
        'status',
    ];

    protected $casts = [
        'settings' => 'array',
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

    public function category()
    {
        return $this->belongsTo(PPOBCategory::class, 'p_p_o_b_category_id');
    }

    public function products()
    {
        return $this->hasMany(PPOBProduct::class, 'p_p_o_b_brand_id');
    }
}
