<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOrder extends Model
{
    use HasFactory;
    protected $table = 'product__orders_details';

    protected $fillable = [
        'order_id',
        'request_id',
        'product_id',
        'user_id',
        'address_id',
        'quantity',
        'requested_flower_price',
        'delivery_charge',
        'total_price',
        'suggestion',
    ];
    
    public function flowerRequest()
    {
        return $this->belongsTo(ProductRequest::class, 'request_id', 'request_id');
    }
    public function subscription()
{
    return $this->hasOne(ProductSucription::class, 'order_id', 'order_id');
}
public function flowerPayments()
{
    return $this->hasMany(ProductPayment::class, 'order_id', 'order_id');
}
public function user()
{
    return $this->belongsTo(User::class, 'user_id', 'userid');
}

public function flowerProduct()
{
    return $this->belongsTo(FlowerProduct::class, 'product_id', 'product_id');
}
public function address()
{
    return $this->belongsTo(UserAddress::class, 'address_id');
}

}
