<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowerPickupDetails extends Model
{
    protected $table = 'flower__pickup_details'; 
    protected $fillable = [
         'pick_up_id', 'vendor_id', 'rider_id', 'pickup_date', 'total_price', 'payment_method','payment_status','payment_id'
    ];

   
    public function vendor() {
        return $this->belongsTo(FlowerVendor::class,'vendor_id', 'vendor_id');
    }

    public function rider() {
        return $this->belongsTo(RiderDetails::class ,'rider_id', 'rider_id');
    }
    public function flowerPickupItems()
{
    return $this->hasMany(FlowerPickupItems::class, 'pick_up_id', 'pick_up_id');
}
}
