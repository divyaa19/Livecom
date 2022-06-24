<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'oc_review';

    const CREATED_AT = 'date_added';
    const UPDATED_AT = 'date_modified';
}
