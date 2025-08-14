<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferOfferClaim extends Model
{
    use HasFactory;

    protected $table = "flower__refer_offer_claim";

    protected $fillable = [
        'offer_id',
        'user_id',
        'selected_pairs',
        'date_time',
        'status',
    ];

     protected $casts = [
        'selected_pairs' => 'array',
        'date_time'      => 'datetime',
    ];
}
