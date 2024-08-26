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

    // /**
    //  * The attributes that are mass assignable.
    //  *
    //  * @var array<int, string>
    //  */
    // // protected $fillable = [
    // //     'userid',
    // //     'name','first_name','last_name','email','phonenumber','dob','password','bloodgrp','qualification',
    // //     'userphoto','fathername','mothername','	marital','spouse','datejoin','seba','templeid','bedhaseba','status',
        
        
    // // ];
    // protected $fillable = [
    //     'userid','first_name','last_name','phonenumber', 'password'
    // ];
    // protected $dates = ['approved_date'];
    // /**
    //  * The attributes that should be hidden for serialization.
    //  *
    //  * @var array<int, string>
    //  */
    // protected $hidden = [
    //     'password',
    //     'remember_token',
    // ];

    // /**
    //  * The attributes that should be cast.
    //  *
    //  * @var array<string, string>
    //  */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];

    // public function isAdmin()
    // {
    //     return $this->role === 'admin';
    // }

    // public function bankdetail()
    // {
    //     return $this->hasOne(Bankdetail::class);
    // }
    // public function bookings()
    // {
    //     return $this->hasMany(Booking::class, 'user_id');
    // }

    // public static function boot()
    // {
    //     parent::boot();

    //     static::updating(function ($model) {
    //         // Check if the status is changing from pending to approved
    //         if ($model->isDirty('status') && $model->status === 'active') {
    //             $model->approved_at = now(); // Set the approved timestamp
    //         }
    //     });
    // }

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $primaryKey = 'userid'; 
    protected $fillable = [
        'userid', 'mobile_number', 'email', 'order_id', 'expiry', 'hash', 'client_id', 'client_secret', 'otp_length', 'channel', 'userphoto',
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

    //    public function isAdmin()
    // {
    //     return $this->role === 'admin';
    // }

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
}
