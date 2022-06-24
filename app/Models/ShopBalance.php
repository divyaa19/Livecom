<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopBalance extends Model
{
    protected $table = 'oc_seller_balance';

    protected $fillable = [
        'seller_unique_id',
        'amount',
        'product_id',
        'store_name',
        'order_id',
        'user_unique_id',
    ];
}
