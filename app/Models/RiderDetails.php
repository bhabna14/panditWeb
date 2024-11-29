<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class RiderDetails extends Model
{
    // use HasFactory;
    use HasFactory, HasApiTokens;

    protected $table = 'flower__rider_details';

    protected $fillable = [
        'rider_id',
        'rider_name',
        'phone_number',
        'rider_img',
        'description',
    ];
    public function pickupDetails()
    {
        return $this->hasMany(FlowerPickupDetails::class, 'rider_id','rider_id');
    }

}
