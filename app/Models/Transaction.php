<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'oc_bid_transaction';

    const CREATED_AT = 'date_added';
    const UPDATED_AT = 'date_modified';

}
