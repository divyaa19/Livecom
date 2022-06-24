<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpdateWallet extends Model
{
    protected $primaryKey = 'wallet_id';

    protected $table = 'oc_wallet';

    const CREATED_AT = 'date_added';
    const UPDATED_AT = 'date_modified';
}
