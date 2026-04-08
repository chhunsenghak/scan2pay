<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_PAID    = 'paid';
    const STATUS_FAILED  = 'failed';

    protected $fillable = [
        'transaction_id',
        'md5',
        'amount',
        'currency',
        'status',
        'bakong_tx_id',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}