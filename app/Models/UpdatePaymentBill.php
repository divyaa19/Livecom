<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpdatePaymentBill extends Model
{
    protected $primaryKey = 'payment_bill_id';

    protected $table = 'oc_payment';

    const UPDATED_AT = 'date_modified';
}
