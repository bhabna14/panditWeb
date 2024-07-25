<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;
    protected $fillable = [
        'address_type', 'area', 'city', 'state', 'pincode', 'country', 'default'
    ];

    public function setAsDefault()
    {
        // Remove default from other addresses
        self::where('user_id', $this->user_id)->update(['default' => false]);

        // Set this address as default
        $this->update(['default' => true]);
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'address_id', 'id');
    }
}
