<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiderLocationTracking extends Model
{
    use HasFactory;

    protected $table = 'rider__location_tracking';

    protected $fillable = [
        'rider_id',
        'latitude',
        'longitude',
        'date_time',
    ];

    protected $casts = [
    'date_time' => 'datetime',
    ];

}
