<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class oc_seller_products_new extends Model
{
    protected $table = 'oc_seller_products_news';
    
    public $timestamps = false;

    const CREATED_AT = 'date_added';
    const UPDATED_AT = 'date_modified';
}