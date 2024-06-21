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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'userid',
    //     'name','first_name','last_name','email','phonenumber','dob','password','bloodgrp','qualification',
    //     'userphoto','fathername','mothername','	marital','spouse','datejoin','seba','templeid','bedhaseba','status',
        
        
    // ];
    protected $fillable = [
        'userid','first_name','last_name','phonenumber', 'password'
    ];
    protected $dates = ['approved_date'];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function bankdetail()
    {
        return $this->hasOne(Bankdetail::class);
    }

    public static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            // Check if the status is changing from pending to approved
            if ($model->isDirty('status') && $model->status === 'active') {
                $model->approved_at = now(); // Set the approved timestamp
            }
        });
    }
}
