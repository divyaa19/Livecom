<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCartList extends Model
{
    protected $table = 'oc_order_cart_list';
    protected $fillable = [
        'variation_1',
        'variation_2',
        'quantity',
        'total_price'
    ];
}
