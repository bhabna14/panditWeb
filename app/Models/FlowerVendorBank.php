<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowerVendorBank extends Model
{
    use HasFactory;

    protected $table = 'flower__vendor_bank';

    protected $fillable = [
        'temple_id', 
        'vendor_id', 
        'bank_name', 
        'account_no', 
        'ifsc_code', 
        'upi_id', 
       
    ];
    public function vendor()
    {
        return $this->belongsTo(FlowerVendor::class, 'vendor_id', 'vendor_id');
    }

    public function vendorBanks()
{
    return $this->hasMany(VendorBank::class, 'vendor_id');
}

}
