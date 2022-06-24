<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BidTransaction extends Model
{
    protected $primaryKey = 'wallet_id';
    protected $table = 'oc_bid_transaction';
    public $incrementing = false;

    const CREATED_AT = 'date_added';
    const UPDATED_AT = 'date_modified';
}
