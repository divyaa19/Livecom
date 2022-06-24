<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    
    protected $primaryKey = 'wallet_id';

    protected $table = 'oc_wallet';
    const UPDATED_AT = 'date_modified';
}
