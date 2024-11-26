<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiderDetails extends Model
{
    use HasFactory;

    protected $table = 'flower__rider_details';

    protected $fillable = [
        'rider_id',
        'rider_name',
        'phone_number',
        'rider_img',
        'description',
    ];

}
