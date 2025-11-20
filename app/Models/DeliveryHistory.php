<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryHistory extends Model
{
    use HasFactory;

    protected $table = 'delivery_history';

    protected $fillable = [
        'order_id',
        'rider_id',
        'delivery_status',
        'delivery_time',
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

    public function deliveryStartHistory()
    {
        return $this->hasOne(DeliveryStartHistory::class, 'rider_id', 'rider_id');
    }
}
