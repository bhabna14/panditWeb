<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class RiderDetails extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'flower__rider_details';

    protected $fillable = [
        'rider_id',
        'rider_name',
        'phone_number',
        'rider_img',
        'salary',
        'dob',
        'documents',
        'description',
        'tracking',
    ];

    protected $casts = [
        'salary'    => 'decimal:2',
        'dob'       => 'date',
        'documents' => 'array', // store JSON array of file paths
    ];

    public function pickupDetails()
    {
        return $this->hasMany(FlowerPickupDetails::class, 'rider_id', 'rider_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'rider_id', 'rider_id');
    }

    public function deliveryHistories()
    {
        return $this->hasMany(DeliveryHistory::class, 'rider_id', 'rider_id');
    }

    public function attendances()
    {
        return $this->hasMany(\App\Models\RiderAttendance::class, 'rider_id', 'rider_id');
    }
}
