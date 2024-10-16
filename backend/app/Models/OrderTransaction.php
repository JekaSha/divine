<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderTransaction extends Pivot
{
    protected $table = 'order_transaction';
}
