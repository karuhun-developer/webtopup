<?php

namespace App\Models\Order;

use App\Enums\DigiflazzStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\Payment\Payment;
use App\Models\PPOB\PPOBBrand;
use App\Models\PPOB\PPOBProduct;
use App\Models\User;
use App\Models\Voucher\VoucherUse;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Order extends Model implements HasMedia
{
    use InteractsWithMedia;

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
        'discount_amount',
        'total_amount',
        'payment_status',
        'topup_status',
        'archive_at',
        'sn',
    ];

    protected $casts = [
        'submited' => 'array',
        'archive_at' => 'datetime',
        'payment_status' => PaymentStatusEnum::class,
        'topup_status' => DigiflazzStatusEnum::class,
    ];

    public function getRouteKeyName()
    {
        return 'reference';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(PPOBProduct::class, 'p_p_o_b_product_id');
    }

    public function brand()
    {
        return $this->belongsTo(PPOBBrand::class, 'p_p_o_b_brand_id');
    }

    public function payment()
    {
        return $this->morphOne(Payment::class, 'payable');
    }

    public function notifications()
    {
        return $this->hasMany(OrderNotification::class);
    }

    public function voucherUse()
    {
        return $this->morphOne(VoucherUse::class, 'usable');
    }

    #[Scope]
    public function onlyArchive(Builder $query)
    {
        return $query->whereNotNull('archive_at');
    }

    #[Scope]
    public function withoutArchive(Builder $query)
    {
        return $query->whereNull('archive_at');
    }
}
