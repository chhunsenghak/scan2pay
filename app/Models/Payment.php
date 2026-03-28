<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'amount',
        'currency',
        'qr_code',
        'md5',
        'hash',
        'short_hash',
        'transaction_id',
        'status',
        'raw_response'
    ];
}
