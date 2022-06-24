<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionProduct extends Model
{
    protected $table = 'oc_promotion_product';

    protected $fillable = [
      'promotion_id',
      'product_id',
    ];
    /*public function productData(){
        return $this->hasMany(Product::class, '');
    }*/

}
