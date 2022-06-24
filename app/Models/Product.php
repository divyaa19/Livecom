<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // use Uuids;

    // protected $primaryKey = 'oc_customer_id';
    protected $table = 'oc_product';

    protected $primaryKey = 'product_id';
    public $timestamps = false;
    protected $fillable = [
        'sell_mode',
        'buy_mode',
        'title',
        'description',
        'category',
        'code',
        'price',
        'stock',
        'date_added',
        'date_modified',
        'store_id',
        'status'
    ];
    

    public function media(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id');
    }

    public function CartList(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(OrderCartList::class, 'product_id', 'product_id');
    }

    public function image(): \Illuminate\Database\Eloquent\Relations\hasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id');
    }

    public function variations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductVariationsData::class, 'variation_id', 'product_id');
    }

    public function variation(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductVariation::class, 'product_id', 'product_id');
    }

    public function buyModeData(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ProductBuyModeData::class,'product_id', 'product_id');
    }

    public function specifications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductSpecifications::class, 'product_id',);
    }

    public function shipment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ProductShipment::class, 'product_id', 'product_id');
    }
}
