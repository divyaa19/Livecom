<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductShipment extends Model
{
    protected $table = 'oc_product_shipment';
    protected $fillable = [
        'product_id',
        'shipment_free',
        'shipment_courier'
    ];

    public function regions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductShipmentRegion::class, 'shipment_id', 'id');
    }
}
