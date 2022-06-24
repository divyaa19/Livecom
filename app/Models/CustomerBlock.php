<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerBlock extends Model
{
    protected $table = 'oc_customer_blocked_list';

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'date_modified';
}
