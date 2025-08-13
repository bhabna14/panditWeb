<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferOffer extends Model
{
    use HasFactory;

    protected $table = "flower__refer_offer";

    protected $fillable = [
        'offer_name',
        'description',
        'no_of_refer',
        'benefit',
        'status',
    ];

     protected $casts = [
        'no_of_refer' => 'array',
        'benefit'     => 'array',
    ];
}
