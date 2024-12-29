<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryCustomizeHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'rider_id',
        'delivery_status',
        'longitude',
        'latitude',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id','order_id');
    }
    
    public function rider()
    {
        return $this->belongsTo(RiderDetails::class,'rider_id', 'rider_id');
    }
}
