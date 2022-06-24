<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowCustomer extends Model
{
    protected $table = 'oc_follow';

    protected $primaryKey = 'follow_by_user_id';

}
