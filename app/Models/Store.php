<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $table = 'oc_purpletree_vendor_stores';

    protected $primaryKey = 'seller_id';

    public $timestamps = false;
}
