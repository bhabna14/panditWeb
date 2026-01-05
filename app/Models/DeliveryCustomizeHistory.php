<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryCustomizeHistory extends Model
{
    use HasFactory;

    protected $table = 'delivery_customize_histories';

    protected $fillable = [
        'request_id',
        'rider_id',
        'delivery_status',
        'longitude',
        'latitude',
    ];

    public function flowerRequest()
    {
        return $this->belongsTo(FlowerRequest::class,'request_id', 'request_id');
    }

    public function rider()
    {
        return $this->belongsTo(RiderDetails::class,'rider_id', 'rider_id');
    }
}
