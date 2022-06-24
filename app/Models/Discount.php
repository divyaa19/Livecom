<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $table = 'discounts';
    protected $fillable = [
        'seller_id',
        'discount_type',
        'discount_amount',
        'percentage_off',
        'product_id'
    ];
}
