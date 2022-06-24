<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveStoreBidHistory extends Model
{
    protected $table = 'oc_live_bid_history';

    public $timestamps = false;
    protected $primaryKey = 'history_id';
    const CREATED_AT = 'date_added';
    const UPDATED_AT = 'date_modified';
}
