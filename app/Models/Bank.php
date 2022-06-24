<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = 'oc_customer_affiliate';

    // protected $primaryKey = 'customer_id';

    public $timestamps = false;
    
    public $incrementing = false;
}
