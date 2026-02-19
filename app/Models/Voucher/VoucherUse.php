<?php

namespace App\Models\Voucher;

use Illuminate\Database\Eloquent\Model;

class VoucherUse extends Model
{
    protected $fillable = [
        'voucher_id',
        'usable_type',
        'usable_id',
        'before_amount',
        'discount_amount',
        'after_amount',
    ];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function usable()
    {
        return $this->morphTo();
    }
}
