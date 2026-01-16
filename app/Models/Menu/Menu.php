<?php

namespace App\Models\Menu;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Menu extends Model
{
    use LogsActivity;

    protected $fillable = [
        'role_id',
        'name',
        'url',
        'icon',
        'order',
        'active_pattern',
        'status',
    ];

    protected $casts = [
        'status' => \App\Enums\CommonStatusEnum::class,
    ];

    public static function booted()
    {
        static::creating(function ($menu) {
            // Clear cache menus
            Cache::forget('menus:role:'.$menu->role_id);
        });
        static::updating(function ($menu) {
            // Clear cache menus
            Cache::forget('menus:role:'.$menu->role_id);
        });
        static::deleting(function ($menu) {
            // Clear cache menus
            Cache::forget('menus:role:'.$menu->role_id);
        });
    }

    // Get the activity log options.
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['*']);
    }

    public function role()
    {
        return $this->belongsTo(\App\Models\Spatie\Role::class);
    }

    public function subMenu()
    {
        return $this->hasMany(MenuSub::class, 'menu_id')->orderBy('order', 'asc');
    }
}
