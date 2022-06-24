<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveStoreProduct extends Model
{
    protected $table = 'oc_livestore_product_session';
    protected $primaryKey = 'session_id';
    public $timestamps = false;
}
