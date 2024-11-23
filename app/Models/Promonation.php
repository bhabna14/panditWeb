<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promonation extends Model
{
    use HasFactory;

    protected $fillable = ['promonation_image', 'date','promo_heading' ,'description','button_title', 'status'];
}
