<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens; // ✅ correct import
use Illuminate\Support\Facades\Hash; // ✅ import the facade

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
        'password',
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

    protected $hidden = ['password', 'otp'];

    protected $casts = [
        'flower_ids'     => 'array',
        'otp_expires_at' => 'datetime',
    ];

    public function getAuthIdentifierName()
    {
        return $this->getKeyName(); // "vendor_id"
    }

    /**
     * Auto-hash on set; avoid double-hashing.
     */
    public function setPasswordAttribute($value): void
    {
        if (!is_null($value) && $value !== '') {
            $this->attributes['password'] = self::looksHashed($value)
                ? $value
                : Hash::make($value); // ✅ now resolves correctly
        }
    }

    private static function looksHashed(string $value): bool
    {
        return str_starts_with($value, '$2y$')
            || str_starts_with($value, '$2a$')
            || str_starts_with($value, '$2b$')
            || str_starts_with($value, '$argon2i$')
            || str_starts_with($value, '$argon2id$');
    }

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
