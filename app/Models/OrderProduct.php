<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $table = 'oc_order_product';

    protected $primaryKey = 'order_product_id';

    public $timestamps = false;
}
