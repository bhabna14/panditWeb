<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowerPickupDetails extends Model
{
    protected $table = 'flower__pickup_details'; 
    protected $fillable = [
        'flower_id', 'unit_id', 'vendor_id', 'rider_id', 'quantity', 'pickup_date', 'price', 'status',
    ];

    public function flower()
    {
        return $this->belongsTo(FlowerProduct::class, 'flower_id', 'product_id');
    }
    

    public function unit() {
        return $this->belongsTo(PoojaUnit::class,'unit_id');
    }

    public function vendor() {
        return $this->belongsTo(FlowerVendor::class,'vendor_id', 'vendor_id');
    }

    public function rider() {
        return $this->belongsTo(RiderDetails::class ,'rider_id', 'rider_id');
    }
}
