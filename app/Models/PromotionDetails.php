<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionDetails extends Model
{
      use HasFactory;

    protected $table = 'flower__promotion_details';

    protected $fillable = [
        'start_date',
        'end_date',
        'header',
        'body',
        'photo',
        'status',
    ];

}
