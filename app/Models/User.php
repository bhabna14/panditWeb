<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'userid',
        'name',
        'user_type',
        'mobile_number',
        'referral_code',
        'code_status',
        'otp',
        'email',
        'order_id',
        'expiry',
        'hash',
        'client_id',
        'client_secret',
        'otp_length',
        'channel',
        'userphoto',
    ];

    protected $hidden = [
        'client_secret',
        'hash',
        'otp',
        'remember_token',
    ];

    protected $casts = [
        'expiry' => 'datetime',
    ];

    /**
     * Sanctum / Auth identifier setup for custom key
     */
    public function getAuthIdentifierName()
    {
        return 'userid';
    }

    public function getAuthIdentifier()
    {
        return $this->userid;
    }

    // 🔗 Relations
    public function devices()
    {
        return $this->hasMany(UserDevice::class, 'user_id', 'userid');
    }

    public function addressDetails()
    {
        return $this->hasOne(UserAddress::class, 'user_id', 'userid')->where('default', 1);
    }

    public function bankdetail()
    {
        return $this->hasOne(Bankdetail::class);
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_user_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id', 'userid');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'userid');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'user_id', 'userid');
    }
}
