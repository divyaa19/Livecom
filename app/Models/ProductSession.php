<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSession extends Model
{
    protected $table = 'oc_product_session';

    const CREATED_AT = 'date_added';
    const UPDATED_AT = 'date_modified';
}
