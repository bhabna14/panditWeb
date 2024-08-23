<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;



class PanditLogin extends Authenticatable
{
   
    use HasApiTokens, HasFactory;
    use Notifiable;
    protected $table = 'pandit_login';

    // Your model properties and methods
    protected $fillable = [
        'otp', 'pandit_id', 'mobile_no',
    ];
    protected $hidden = [
        'otp',
    ];


    public function devices()
    {
        return $this->hasMany(PanditDevice::class, 'pandit_id', 'pandit_id');
    }
    // Add any other model-specific logic here
}
