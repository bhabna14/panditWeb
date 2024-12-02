<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserUnauthorisedDevices extends Model
{
    use HasFactory;
   
    protected $table = 'user_unauthorised_devices';
   
}
