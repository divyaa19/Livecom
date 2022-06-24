<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $primaryKey = 'user_id';

    protected $table = 'oc_customer';

    public $timestamps = false;
}
