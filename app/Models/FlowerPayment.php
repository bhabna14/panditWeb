<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowerPayment extends Model
{
    use HasFactory;

    protected $table = 'flower_payments'; // table name

    // NOTE: 'paid_amount' is used as the amount to be collected/that was collected.
    protected $fillable = [
        'order_id',
        'payment_id',
        'user_id',
        'payment_method',
        'paid_amount',
        'payment_status',
        'received_by',
    ];

    protected $casts = [
        'paid_amount' => 'float',
    ];
}
