<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowerVendor extends Model
{
    use HasFactory;

    protected $table = 'flower__vendor_details';

    protected $fillable = [
        'temple_id', 
        'vendor_id', 
        'vendor_name', 
        'phone_no', 
        'email_id', 
        'vendor_category', 
        'payment_type', 
        'vendor_gst', 
        'vendor_address', 
       
    ];

    public function vendorBanks()
    {
        return $this->hasMany(FlowerVendorBank::class, 'vendor_id', 'vendor_id'); // Adjust as necessary
    }
    

}


