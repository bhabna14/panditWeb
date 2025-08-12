<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FLowerReferal extends Model
{
    use HasFactory;

    protected $table = 'flower_referrals';
    
    protected $fillable = ['user_id','referrer_user_id','status'];

}
