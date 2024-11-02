<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';

    protected $fillable = [
        'product_id',
        'user_id',
        'quantity',
        'total_price',
        'order_id',  // Add this line
        'address_id'
    ];
}
