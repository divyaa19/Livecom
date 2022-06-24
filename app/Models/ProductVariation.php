<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;

class ProductVariation extends Model
{
    // use Uuids;

    protected $table = 'oc_product_variations';
    public $incrementing = false;
    protected $fillable = [
        'variation_id',
        'type',
        'variation',
        'variation_price',
        'variation_stock'
    ];
}
