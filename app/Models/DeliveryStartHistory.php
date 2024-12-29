<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryStartHistory extends Model
{
    use HasFactory;
    protected $table = 'delivery_start_histories';

    protected $fillable = [
        
        'rider_id',
        'start_delivery_time',
       
    ];
}
