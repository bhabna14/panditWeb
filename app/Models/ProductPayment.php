<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPayment extends Model
{
    use HasFactory;
    protected $table = 'product__payments_details'; // Specify the table name

    protected $fillable = [
        'order_id',
        'payment_id',
        'user_id',
        'payment_method',
        'paid_amount',
        'payment_status',
    ];
}
