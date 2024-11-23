<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowerRequest extends Model
{
    use HasFactory;
    protected $table = 'flower_requests';

    // Fillable fields for mass assignment
    protected $fillable = [
        'request_id',
        'product_id',
        'user_id',
        'address_id',
        'description',
        'suggestion',
        'date',
        'time',
        'status',
    ];

    public function order()
    {
        return $this->hasOne(Order::class, 'request_id', 'request_id');
    }
    public function address()
    {
        return $this->belongsTo(UserAddress::class, 'address_id','id');
    }

    public function user()
    {
         return $this->belongsTo(User::class, 'user_id', 'userid');
    }
    public function flowerProduct()
    {
        return $this->belongsTo(FlowerProduct::class, 'product_id', 'product_id');
    }

    // FlowerRequest.php
    public function flowerRequestItems()
    {
        return $this->hasMany(FlowerRequestItem::class, 'flower_request_id', 'request_id');
    }

}
