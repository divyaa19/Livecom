<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveStreamsParticipants extends Model
{
    protected $table = 'oc_stream_product_participants';

    public $timestamps = false;

    const CREATED_AT = 'date_added';
    const UPDATED_AT = 'date_modified';
}
