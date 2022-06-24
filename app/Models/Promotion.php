<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $table = 'oc_seller_promotions';
    protected $fillable = [
        'seller_id',
        'discount_id',
        'set_private',
        'promotion_type',
        'promotion_name',
        'promotion_code',
        'start_date',
        'end_date',
        'unit_limitation',
        'voucher_limitation',
        'discount_type',
        'discount_amount',
        'minimum_spend',
        'active_immediately',
        'status'
    ];
    
    public function product(){
        return $this->hasMany(PromotionProduct::class, 'promotion_id', 'id');
    }

}
