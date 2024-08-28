<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $table = "pandit_profile";
    protected $fillable = ['pandit_id', 'otp', 'phonenumber', 'title', 'name', 'email', 'whatsappno', 'bloodgroup', 'profile_photo', 'maritalstatus', 'language','agree'];

    public function poojadetails()
    {
        return $this->hasMany(Poojadetails::class, 'pandit_id', 'pandit_id');
    }
}
