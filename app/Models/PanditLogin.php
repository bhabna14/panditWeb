<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class PanditLogin extends Authenticatable
{
    use Notifiable;
    protected $table = 'pandit_login';

    // Your model properties and methods
    protected $fillable = [
        'otp', 'pandit_id', 'mobile_no',
    ];

    // Add any other model-specific logic here
}
