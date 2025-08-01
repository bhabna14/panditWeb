<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowerCalendor extends Model
{
    use HasFactory;

    protected $table = 'flower__festival_calendar';

    protected $fillable = [
        'festival_name',
        'festival_date',
        'festival_image',
        'related_flower',
        'package_price',
        'description',
    ];
}
