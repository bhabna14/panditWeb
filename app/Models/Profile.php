<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $table = "profiles";

    protected $fillable = [
        'profile_id',
        'title',
        'name',
        'email',
        'whatsappno',
        'bloodgroup',
        'profile_photo',
        'maritalstatus',
        'language',
        // Add more fillable fields as needed
    ];
}
