<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    use Notifiable;
    /**     *
     * @var array
     */
    // protected $primaryKey = 'userid'; 
    protected $fillable = [
        'userid','name', 'mobile_number', 'otp','email', 'order_id', 'expiry', 'hash', 'client_id', 'client_secret', 'otp_length', 'channel', 'userphoto',
    ];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'client_secret', 'hash',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'expiry' => 'datetime',
    ];

    public function bankdetail()
    {
        return $this->hasOne(Bankdetail::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id');
    }

    public function devices()
    {
        return $this->hasMany(UserDevice::class, 'user_id', 'userid');
    }

    public function addressDetails()
{
    return $this->hasOne(UserAddress::class, 'user_id', 'userid')->where('default', 1);
}

}
