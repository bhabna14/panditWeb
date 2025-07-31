<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferDetails extends Model
{
    use HasFactory;

    protected $table = 'offer_details';

    protected $fillable = [
       
        'main_header',
        'sub_header',
        'content',
        'start_date',
        'end_date',
        'discount',
        'menu',
        'image'
    ];
}
