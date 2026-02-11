<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class OrderNotification extends Model
{
    protected $fillable = [
        'order_id',
        'provider',
        'title',
        'content',
        'error',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
