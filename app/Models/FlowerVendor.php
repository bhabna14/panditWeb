<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowerVendor extends Model
{
    use HasFactory;

    protected $primaryKey = 'vendor_id';   // If vendor_id is your PK
    public $incrementing = false;          // Because it's string, not auto-increment
    protected $keyType = 'string';

    protected $table = 'flower__vendor_details';

    protected $fillable = [
        'vendor_id', 
        'vendor_name', 
        'phone_no', 
        'email_id', 
        'vendor_category', 
        'payment_type', 
        'vendor_gst', 
        'vendor_address',
        'flower_ids',
    ];

     protected $casts = [
        'flower_ids' => 'array',
    ];

     public function monthPrices()
    {
        return $this->hasMany(MonthWiseFlowerPrice::class, 'vendor_id', 'vendor_id')
            ->with(['product:product_id,name', 'unit:id,unit_name']);
    }

public function flowerProduct(){
    return $this->hasMany(FlowerProduct::class,'flower_ids', 'product_id');
}


    public function vendorBanks()
    {
        return $this->hasMany(FlowerVendorBank::class, 'vendor_id', 'vendor_id'); // Adjust as necessary
    }
    public function pickupDetails()
    {
        return $this->hasMany(FlowerPickupDetails::class,'vendor_id', 'vendor_id');
    }

    

}


