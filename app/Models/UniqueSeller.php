<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniqueSeller extends Model
{
    protected $table = 'oc_purpletree_vendor_stores';

    protected $primaryKey = 'seller_unique_id';

    public $timestamps = false;
}
