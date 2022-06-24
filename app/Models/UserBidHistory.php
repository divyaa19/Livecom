<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBidHistory extends Model
{
    protected $table = 'oc_live_bid_history';

    const CREATED_AT = 'date_added';
    const UPDATED_AT = 'date_modified';
}
