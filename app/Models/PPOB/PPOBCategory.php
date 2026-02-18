<?php

namespace App\Models\PPOB;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class PPOBCategory extends Model implements HasMedia
{
    use HasSlug, InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'description',
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

    public function brands()
    {
        return $this->hasMany(PPOBBrand::class, 'p_p_o_b_category_id');
    }

    public function activeBrands()
    {
        return $this->hasMany(PPOBBrand::class, 'p_p_o_b_category_id')->where('status', true);
    }
}
