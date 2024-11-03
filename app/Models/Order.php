<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';

    protected $fillable = [
        'request_id',
        'product_id',
        'user_id',
        'quantity',
        'total_price',
        'order_id',  // Add this line
        'address_id',
        'suggestion'
    ];
    public function flowerRequest()
    {
        return $this->belongsTo(FlowerRequest::class, 'request_id', 'request_id');
    }
    public function subscription()
{
    return $this->hasOne(Subscription::class, 'order_id', 'order_id');
}

}
