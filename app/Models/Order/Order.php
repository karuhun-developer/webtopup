<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'p_p_o_b_brand_id',
        'p_p_o_b_product_id',
        'reference',
        'ref_number',
        'name',
        'email',
        'phone',
        'note',
        'submited',
        'amount',
        'fee',
        'total_amount',
        'payment_status',
        'topup_status',
    ];

    protected $casts = [
        'submited' => 'array',
        'payment_status' => \App\Enums\PaymentStatusEnum::class,
        'topup_status' => \App\Enums\DigiflazzStatusEnum::class,
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\PPOB\PPOBProduct::class, 'p_p_o_b_product_id');
    }

    public function brand()
    {
        return $this->belongsTo(\App\Models\PPOB\PPOBBrand::class, 'p_p_o_b_brand_id');
    }

    public function payment()
    {
        return $this->morphOne(\App\Models\Payment\Payment::class, 'payable');
    }
}
