<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens; // ✅ correct import

class FlowerVendor extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'flower__vendor_details';

    protected $primaryKey = 'vendor_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'vendor_id',
        'vendor_name',
        'phone_no',
        'otp',
        'email_id',
        'vendor_category',
        'payment_type',
        'vendor_gst',
        'vendor_address',
        'flower_ids',
        'date_of_joining',
        'vendor_document',
        'otp_expires_at',
        'otp_attempts',
        'status',
    ];

    protected $casts = [
        'flower_ids'     => 'array',
        'otp_expires_at' => 'datetime',
    ];

    protected $hidden = ['otp'];

    /**
     * Since your PK is vendor_id (not "id"), this helps some auth flows.
     * Sanctum itself uses Eloquent keys, but this makes it explicit.
     */
    public function getAuthIdentifierName()
    {
        return $this->getKeyName(); // returns "vendor_id"
    }

    /* ---------- Relationships (unchanged) ---------- */

    public function monthPrices()
    {
        return $this->hasMany(MonthWiseFlowerPrice::class, 'vendor_id', 'vendor_id')
            ->with(['product:product_id,name', 'unit:id,unit_name']);
    }

    public function flowerProduct()
    {
        return $this->hasMany(FlowerProduct::class, 'flower_ids', 'product_id');
    }

    public function vendorBanks()
    {
        return $this->hasMany(FlowerVendorBank::class, 'vendor_id', 'vendor_id');
    }

    public function pickupDetails()
    {
        return $this->hasMany(FlowerPickupDetails::class, 'vendor_id', 'vendor_id');
    }
}
