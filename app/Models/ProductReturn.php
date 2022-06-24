<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReturn extends Model
{
    protected $table = 'oc_return';

    const CREATED_AT = 'date_added';
    const UPDATED_AT = 'date_modified';
}
