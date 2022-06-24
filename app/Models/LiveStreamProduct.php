<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveStreamProduct extends Model
{
    protected $table = 'oc_stream_product_details';

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'date_updated';
}
